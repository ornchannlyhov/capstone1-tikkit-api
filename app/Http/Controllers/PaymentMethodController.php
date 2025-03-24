<?php

namespace App\Http\Controllers;

use App\Models\TicketOption;
use App\Models\PaymentMethod;
use App\Models\PurchasedTicket;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Symfony\Component\Process\Process;

class PaymentMethodController extends Controller
{
    // Display available payment methods
    public function index()
    {
        try {
            $paymentMethods = PaymentMethod::where('is_active', true)->get();
            return response()->json(['payment_methods' => $paymentMethods], 200);
        } catch (Exception $e) {
            Log::error("Error fetching payment methods: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch payment methods.'], 500);
        }
    }

    // Process payment
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|exists:payment_methods,code',
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string|max:3',
            'order_id' => 'required|exists:orders,id',
            'stripeToken' => 'sometimes|required_if:payment_method,visa',
            'khqr_code' => 'required_if:payment_method,khqr', 
        ]);

        $paymentMethod = PaymentMethod::where('code', $request->payment_method)->first();
        $orderId = $request->order_id;
        $order = Order::with('carts.ticketOption.ticketOffers')->findOrFail($orderId);

        try {
            switch ($paymentMethod->code) {
                case 'visa':
                    return $this->processVisaPayment($request, $order);
                case 'aba':
                    return $this->processAbaPayment($request, $order);
                case 'khqr':
                    return $this->processKhqrPayment($request, $order);
                default:
                    return response()->json(['error' => 'Invalid payment method'], 400);
            }
        } catch (Exception $e) {
            Log::error("Error processing payment: " . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    // Visa Payment (Using Stripe)
    private function processVisaPayment(Request $request, Order $order)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Apply offer discount if available
            $amount = $this->applyOfferDiscount($request->amount, $order);

            $charge = Charge::create([
                'amount' => $amount * 100,
                'currency' => $request->currency,
                'source' => $request->stripeToken,
                'description' => 'Payment for order #' . $request->order_id,
            ]);

            if ($charge->status == 'succeeded') {
                return $this->createPurchasedTickets($order);
            } else {
                return response()->json(['error' => 'Visa payment failed'], 400);
            }
        } catch (\Stripe\Exception\CardException $e) {
            Log::error("Stripe error: " . $e->getMessage());
            return response()->json(['error' => 'Card error: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error("Stripe error: " . $e->getMessage());
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // ABA Payment (PayWay)
    private function processAbaPayment(Request $request, Order $order)
    {
        $request->validate([
            'req_time' => 'required|date_format:YmdHis',
            'tran_id' => 'required|string|max:20',
            'payment_option' => 'required|string|max:20',
            'hash' => 'required|string',
        ]);

        $apiEndpoint = config('services.payway.api_endpoint');
        $merchantId = config('services.payway.merchant_id');
        $secretKey = config('services.payway.secret_key');

        // Apply offer discount if available
        $amount = $this->applyOfferDiscount($request->amount, $order);

        $params = [
            'req_time' => $request->req_time,
            'merchant_id' => $merchantId,
            'tran_id' => $request->tran_id,
            'amount' => $amount,
            'payment_option' => $request->payment_option,
            'type' => 'purchase',
            'currency' => $request->currency,
        ];

        // Reconstruct the string used for generating the hash
        $hashString = $params['req_time'] . $merchantId . $params['tran_id'] . $amount . $params['payment_option'] . 'purchase' . $params['currency'] . $secretKey;

        $expectedHash = hash('sha256', $hashString);

        // Validate the hash
        if ($request->hash !== $expectedHash) {
            Log::error("ABA payment hash validation failed.");
            return response()->json(['error' => 'Invalid ABA payment hash'], 400);
        }

        try {
            $client = new Client();
            $response = $client->post($apiEndpoint, [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'multipart' => $this->formatMultipartData($params),
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['status']) && $responseBody['status']['code'] === '00') {
                return $this->createPurchasedTickets($order);
            } else {
                return response()->json(['error' => 'ABA payment failed'], 400);
            }
        } catch (Exception $e) {
            Log::error("PayWay API request failed: " . $e->getMessage());
            return response()->json(['error' => 'ABA payment request failed'], 500);
        }
    }

    // KHQR Payment Process
    private function processKhqrPayment(Request $request, Order $order)
    {
        $khqrCode = $request->input('khqr_code');
        $amount = $request->amount;
        $amount = $this->applyOfferDiscount($amount, $order);

        try {

            // 1.  Verify KHQR content (using SDK function)
            $khqrResponse = $this->verifyKhqrCode($khqrCode);

            if ($khqrResponse['code'] != 0) { // Verification failed
                Log::error("KHQR Verification failed: " . $khqrResponse['message']);
                return response()->json(['error' => 'KHQR verification failed: ' . $khqrResponse['message']], 400);
            }

            // 2. Decode KHQR to extract needed information
            $decodeResult = $this->decodeKhqrCode($khqrCode);

            if ($decodeResult['code'] != 0) {
                Log::error("KHQR Decode failed: " . $decodeResult['message']);
                return response()->json(['error' => 'KHQR decode failed: ' . $decodeResult['message']], 400);
            }

            // 3. Validate Amount. Check amount in KHQR against order total
            if (floatval($decodeResult['data']['transactionAmount']) != floatval($amount)) {
                Log::error("KHQR Amount mismatch:  Decoded amount: " . $decodeResult['data']['transactionAmount'] . " Order amount: " . $amount);
                return response()->json(['error' => 'KHQR amount mismatch'], 400);
            }

            // 4.  If Verification and amount checks pass, Create purchased tickets
            return $this->createPurchasedTickets($order);

        } catch (Exception $e) {
            Log::error("KHQR Payment processing error: " . $e->getMessage());
            return response()->json(['error' => 'KHQR payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    // Helper function to verify KHQR using SDK
    private function verifyKhqrCode($khqrCode)
    {
        try {
            $verified = $this->runJavaScriptVerification($khqrCode, 'verify');  //Pass Verify or Decode
            return $verified;

        } catch (Exception $e) {
            Log::error("KHQR Verification failed with Javascript Call : " . $e->getMessage());
            return ['code' => 1, 'message' => 'KHQR Verification process failed.'];
        }
    }

    // Helper function to decode KHQR using SDK
    private function decodeKhqrCode($khqrCode)
    {
        try {
            $decode = $this->runJavaScriptVerification($khqrCode, 'decode');
            return (array) $decode;

        } catch (Exception $e) {
            Log::error("KHQR decode failed with Javascript Call : " . $e->getMessage());
            return ['code' => 1, 'message' => 'KHQR decode process failed.'];
        }
    }

    // Helper function to run Javascript process
    private function runJavaScriptVerification($khqrCode, $action)
    {
        $nodeScript = base_path('khqr.js'); // Path to your Node.js script

        //Use this parameter, You must use node khqr.js verify '<KHQR_STRING>'
        $command = "node " . escapeshellarg($nodeScript) . " " . escapeshellarg($action) . " " . escapeshellarg($khqrCode);

        // Using Symfony Process Component
        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("KHQR Javascript Process Failed : " . $process->getErrorOutput());
            return ['code' => 1, 'message' => 'KHQR Javascript Process failed: ' . $process->getErrorOutput()];
        }

        $output = $process->getOutput();

        try {
            $res = json_decode($output, true);
            return $res;
        } catch (Exception $e) {
            Log::error("KHQR JSON decode error Javascript output KHQR: " . $output);
            return ['code' => 1, 'message' => 'KHQR JSON decode error: ' . $e->getMessage()];
        }
    }

    private function applyOfferDiscount($amount, Order $order)
    {
        try {
            $totalDiscount = 0;
            foreach ($order->carts as $cart) {
                $ticketOption = $cart->ticketOption;
                $offer = $ticketOption->ticketOffers()->active()->first();

                if ($offer) {
                    // Calculate discount based on the price of the individual ticket option
                    $ticketPrice = $ticketOption->price;

                    if ($offer->type === 'percentage') {
                        $discountAmount = ($ticketPrice * $cart->quantity) * ($offer->discount / 100);
                    } elseif ($offer->type === 'flat') {
                        $discountAmount = min($offer->discount, $ticketPrice * $cart->quantity);
                    } else {
                        $discountAmount = 0;
                    }
                    $totalDiscount += $discountAmount;
                }
            }

            $amount -= $totalDiscount;
            return max($amount, 0);
        } catch (Exception $e) {
            throw new Exception("Failed to apply offer discount: " . $e->getMessage());
        }
    }

    // Create purchased tickets and update order status
    private function createPurchasedTickets(Order $order)
    {
        DB::beginTransaction();
        try {
            $tickets = [];

            foreach ($order->carts as $cart) {
                $ticketOption = $cart->ticketOption;
                // Get offer information
                $offer = $ticketOption->ticketOffers()->active()->first();

                if ($ticketOption->quantity < $cart->quantity) {
                    throw new Exception('Not enough tickets available.');
                }

                // Reduce ticket stock
                $ticketOption->decrement('quantity', $cart->quantity);

                // Reduce offer quantity if applicable
                if ($offer) {
                    $offer->decrement('quantity', $cart->quantity);
                    $offer->save();
                }

                // Reduce offer usage limit if applicable
                if ($offer && $offer->usage_limit !== null) {
                    $offer->decrement('usage_limit', $cart->quantity);
                }

                // Create Purchased Tickets
                for ($i = 0; $i < $cart->quantity; $i++) {
                    $uniqueHash = Str::uuid()->toString();
                    $purchasedTicket = PurchasedTicket::create([
                        'ticket_id' => $ticketOption->id,
                        'user_id' => auth()->id(),
                        'offer_id' => $offer ? $offer->id : null,
                        'qr_code' => $uniqueHash,
                        'status' => 'valid',
                    ]);
                    $tickets[] = $purchasedTicket;
                }
            }

            // Update Order Status to completed
            $order->update(['status' => 'completed']);

            DB::commit();
            return response()->json([
                'message' => 'Payment successful.',
                'tickets' => $tickets,
                'order_status' => 'completed'
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to process order: ' . $e->getMessage()], 500);
        }
    }

    private function formatMultipartData(array $params): array
    {
        return array_map(fn($key, $value) => ['name' => $key, 'contents' => $value], array_keys($params), $params);
    }
}