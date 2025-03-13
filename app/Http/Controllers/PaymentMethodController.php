<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Stripe\Stripe;
use Stripe\Charge;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    // Display available payment methods
    public function index()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        return response()->json(['payment_methods' => $paymentMethods], 200);
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
        ]);

        $paymentMethod = PaymentMethod::where('code', $request->payment_method)->first();

        try {
            switch ($paymentMethod->code) {
                case 'visa':
                    return $this->processVisaPayment($request);
                case 'aba':
                    return $this->processAbaPayment($request);
                case 'cod':
                    return $this->processCashOnDelivery($request);
                default:
                    return response()->json(['error' => 'Invalid payment method'], 400);
            }
        } catch (Exception $e) {
            Log::error("Payment processing error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    // Visa Payment (Using Stripe)
    private function processVisaPayment(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => $request->amount * 100, // Stripe handles amounts in cents
                'currency' => $request->currency,
                'source' => $request->stripeToken,
                'description' => 'Payment for order #' . $request->order_id,
            ]);

            if ($charge->status == 'succeeded') {
                return response()->json(['message' => 'Visa payment successful', 'transaction_id' => $charge->id], 200);
            } else {
                return response()->json(['error' => 'Visa payment failed'], 400);
            }

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json(['error' => 'Card error: ' . $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error("Stripe error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        }
    }

    // ABA Payment (PayWay)
    private function processAbaPayment(Request $request)
    {
        $request->validate([
            'req_time' => 'required|date_format:YmdHis',
            'tran_id' => 'required|string|max:20',
            'payment_option' => 'required|string|max:20',
            'hash' => 'required|string',
            //'items' => 'nullable|string|max:500', // if you using items, require it
        ]);

        $apiKey = config('services.payway.api_key');
        $apiEndpoint = config('services.payway.api_endpoint');

        $params = [
            'req_time' => $request->req_time,
            'merchant_id' => config('services.payway.merchant_id'),
            'tran_id' => $request->tran_id,
            'amount' => $request->amount,
            'payment_option' => $request->payment_option,
            'hash' => $request->hash,
            'type' => 'purchase', // Default value
            'currency' => $request->currency, // Use from config or request

            //Optinal if required
            'firstname' => $request->firstname ?? null,
            'lastname' => $request->lastname ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'items' => $request->items ?? null,
            'return_url' => $request->return_url ?? null,
            'cancel_url' => $request->cancel_url ?? null,
            'continue_success_url' => $request->continue_success_url ?? null,
            'return_deeplink' => $request->return_deeplink ?? null,
            'custom_fields' => $request->custom_fields ?? null,
            'return_param' => $request->return_param ?? null,
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
                return response()->json([
                    'message' => 'ABA payment initiated successfully',
                    'data' => $responseBody
                ], 200);
            } else {
                Log::error("PayWay API error: " . json_encode($responseBody));
                return response()->json(['error' => 'ABA payment failed: ' . ($responseBody['status']['message'] ?? 'Unknown error')], 400);
            }

        } catch (Exception $e) {
            Log::error("PayWay API request failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'ABA payment request failed: ' . $e->getMessage()], 500);
        }
    }

    private function formatMultipartData(array $params): array
    {
        $multipart = [];
        foreach ($params as $key => $value) {
            if ($value !== null) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }
        return $multipart;
    }

    // Cash on Delivery
    private function processCashOnDelivery(Request $request)
    {
        return response()->json(['message' => 'Cash on delivery selected. Order processing.'], 200);
    }

    // Function to generate ABA Hash (as per PayWay documentation)
    public static function generateAbaHash(array $params, string $publicKey): string
    {

        $stringToHash =
            $params['req_time'] .
            config('services.payway.merchant_id') .
            $params['tran_id'] .
            $params['amount'];

        if (isset($params['items'])) {
            $stringToHash .= $params['items'];
        }

        $hash = hash_hmac('sha512', $stringToHash, $publicKey, true);
        return base64_encode($hash);
    }
}