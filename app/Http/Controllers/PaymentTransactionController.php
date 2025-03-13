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
    // Admin: View all transactions (web route)
    public function index()
    {
        $transactions = PaymentTransaction::with(['user', 'order'])->get();
        return view('dashboard.payment.index', compact('transactions'));
    }

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

            // Retrieve the order and check if the user is authorized and the order is in a pending state.
            $order = Order::where('id', $request->order_id)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();


            // Check if payment amount matches order total
            if ($order->total != $request->amount) {
                return response()->json(['error' => 'Payment amount does not match order total'], 400);
            }

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

            // Update order status to paid
            $order->update(['status' => 'paid']);

            ActivityLogHelper::logActivity(auth()->user(), 'Completed a payment', "Transaction ID: {$transaction->id}");

            return response()->json(['message' => 'Payment transaction recorded successfully', 'transaction' => $transaction], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to process payment', 'details' => $e->getMessage()], 500);
        }
    }
}