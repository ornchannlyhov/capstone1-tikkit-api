<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\Order;
use App\Helpers\ActivityLogHelper;
use Exception;
use Carbon\Carbon;

class PaymentTransactionController extends Controller
{
    // Store a new payment transaction
    public function store(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'method_id' => 'required|exists:payment_methods,id',
                'amount' => 'required|numeric|min:0',
                'reference' => 'required|unique:payment_transactions,reference',
                'currency' => 'required|string|max:10',
            ]);

            $order = Order::where('id', $request->order_id)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();

            // Create the payment transaction
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'method_id' => $request->method_id,
                'status' => 'processing',
                'amount' => $request->amount,
                'reference' => $request->reference,
                'date' => Carbon::now(),
                'currency' => $request->currency,
            ]);

            ActivityLogHelper::logActivity(auth()->user(), 'Initiated a payment', "Transaction ID: {$transaction->id}");

            return response()->json(['message' => 'Payment transaction recorded', 'transaction' => $transaction], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to process payment', 'details' => $e->getMessage()], 500);
        }
    }
}
