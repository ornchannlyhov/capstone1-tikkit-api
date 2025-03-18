<?php

namespace App\Http\Controllers;

use App\Models\TicketOffer;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\PurchasedTicket;
use App\Models\TicketOption;
use App\Models\Offer;  // Add Offer model
use Stripe\Stripe;
use Stripe\Charge;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

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
            'ticket_option_id' => 'required|exists:ticket_options,id',
            'offer_id' => 'nullable|exists:offers,id',
            'stripeToken' => 'sometimes|required_if:payment_method,visa',
        ]);

        $paymentMethod = PaymentMethod::where('code', $request->payment_method)->first();
        $offer = null;

        try {
            if ($request->has('offer_id')) {
                $offer = TicketOffer::active()->find($request->offer_id);
                if (!$offer) {
                    return response()->json(['error' => 'Invalid or expired offer'], 400);
                }
                if ($offer->usage_limit <= 0) {
                    return response()->json(['error' => 'Offer usage limit reached'], 400);
                }
            }

            switch ($paymentMethod->code) {
                case 'visa':
                    return $this->processVisaPayment($request, $offer);  
                case 'aba':
                    return $this->processAbaPayment($request, $offer);  
                default:
                    return response()->json(['error' => 'Invalid payment method'], 400);
            }
        } catch (Exception $e) {
            Log::error("Error processing payment: " . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    // Visa Payment (Using Stripe)
    private function processVisaPayment(Request $request, $offer)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Apply offer discount if available
            $amount = $offer ? $this->applyOfferDiscount($request->amount, $offer) : $request->amount;

            $charge = Charge::create([
                'amount' => $amount * 100, 
                'currency' => $request->currency,
                'source' => $request->stripeToken,
                'description' => 'Payment for order #' . $request->order_id,
            ]);

            if ($charge->status == 'succeeded') {
                // *** IMPORTANT: Create Purchased Ticket AFTER successful payment ***
                return $this->createPurchasedTicket($request->ticket_option_id, $offer);
            } else {
                return response()->json(['error' => 'Visa payment failed'], 400);
            }

        } catch (\Stripe\Exception\CardException $e) {
            Log::error("Stripe error: " . $e->getMessage());
            return response()->json(['error' => 'Card error: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error("Stripe error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // ABA Payment (PayWay)
    private function processAbaPayment(Request $request, $offer)
    {
        $request->validate([
            'req_time' => 'required|date_format:YmdHis',
            'tran_id' => 'required|string|max:20',
            'payment_option' => 'required|string|max:20',
            'hash' => 'required|string',
        ]);

        $apiKey = config('services.payway.api_key');
        $apiEndpoint = config('services.payway.api_endpoint');

        // Apply offer discount if available
        $amount = $offer ? $this->applyOfferDiscount($request->amount, $offer) : $request->amount;

        $params = [
            'req_time' => $request->req_time,
            'merchant_id' => config('services.payway.merchant_id'),
            'tran_id' => $request->tran_id,
            'amount' => $amount,
            'payment_option' => $request->payment_option,
            'hash' => $request->hash,
            'type' => 'purchase',
            'currency' => $request->currency,
        ];

        try {
            $client = new Client();
            $response = $client->post($apiEndpoint, [
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                ],
                'multipart' => $this->formatMultipartData($params)
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['status']) && $responseBody['status']['code'] === '00') {
                // Payment Successful
                // *** IMPORTANT: Create Purchased Ticket AFTER successful payment ***
                return $this->createPurchasedTicket($request->ticket_option_id, $offer);
            } else {
                Log::error("PayWay API error: " . json_encode($responseBody));
                return response()->json(['error' => 'ABA payment failed: ' . ($responseBody['status']['message'] ?? 'Unknown error')], 400);
            }

        } catch (Exception $e) {
            Log::error("PayWay API request failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'ABA payment request failed: ' . $e->getMessage()], 500);
        }
    }

    private function applyOfferDiscount($amount, $offer)
    {
        try {
            // Example: Apply a flat discount or percentage discount based on the offer
            if ($offer->type === 'percentage') {
                return $amount - ($amount * ($offer->discount / 100));
            } elseif ($offer->type === 'flat') {
                return $amount - $offer->discount;
            }
            return $amount;
        } catch (Exception $e) {
            Log::error("Error applying offer discount: " . $e->getMessage());
            throw new Exception("Failed to apply offer discount");
        }
    }

    // Helper function to create a purchased ticket
    private function createPurchasedTicket($ticketOptionId, $offer)
    {
        DB::beginTransaction();
        try {
            $ticketOption = TicketOption::findOrFail($ticketOptionId);
            if ($ticketOption->quantity <= 0) {
                throw new Exception('Sold out');
            }

            // Decrease available quantity of the ticket option
            $ticketOption->decrement('quantity', 1);

            if ($offer && $offer->usage_limit !== null) {
                // Decrease the offer's usage limit
                $offer->decrement('usage_limit', 1);
            }

            $uniqueHash = Str::uuid()->toString();

            $purchasedTicket = PurchasedTicket::create([
                'ticket_id' => $ticketOption->id,
                'user_id' => auth()->id(),
                'offer_id' => $offer ? $offer->id : null, 
                'qr_code' => $uniqueHash,
                'status' => 'valid',
            ]);

            // Generate QR code image (base64 encoded PNG)
            $qrCode = base64_encode(QrCode::format('png')->size(300)->generate(json_encode([
                'ticket_id' => $purchasedTicket->id,
                'hash' => $uniqueHash,
            ])));

            DB::commit();

            return response()->json([
                'message' => 'Payment successful. Ticket created.',
                'ticket_id' => $purchasedTicket->id,
                'qr_code' => $qrCode,
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Error creating purchased ticket: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to create purchased ticket: ' . $e->getMessage()], 500);
        }
    }

    // Format the multipart data for PayWay API request
    private function formatMultipartData(array $params): array
    {
        $multipart = [];
        foreach ($params as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        return $multipart;
    }
}
