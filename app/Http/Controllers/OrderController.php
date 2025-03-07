<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogHelper;
use Exception;

class OrderController extends Controller
{
    // Admin: View all orders (web interface)
    public function index()
    {
        $orders = Order::with('user')->get();
        return view('admin.orders.index', compact('orders')); // Admin can only view orders
    }

    // Admin: View a specific order (web interface)
    public function show($id)
    {
        $order = Order::with('user')->findOrFail($id);
        return view('admin.orders.show', compact('order')); // Admin can only view a specific order
    }

    // Admin: View all cancellation requests (web interface)
    public function viewCancellationRequests()
    {
        try {
            $cancellationRequests = Order::where('status', 'cancel_request')->get();
            return response()->json(['cancellation_requests' => $cancellationRequests], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve cancellation requests', 'details' => $e->getMessage()], 500);
        }
    }

    // Vendor: View all vendor's orders (API)
    public function vendorIndex()
    {
        $orders = Order::where('user_id', auth()->id())->get();
        return response()->json(['orders' => $orders], 200);
    }

    // Vendor: View a specific vendor's order (API)
    public function vendorShow($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        return response()->json(['order' => $order], 200);
    }

    // Vendor: View cancellation requests for their own orders (API)
    public function vendorCancellationRequests()
    {
        try {
            $cancellationRequests = Order::where('user_id', auth()->id())
                ->where('status', 'cancel_request')
                ->get();

            return response()->json(['cancellation_requests' => $cancellationRequests], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve cancellation requests', 'details' => $e->getMessage()], 500);
        }
    }

    // Vendor: Accept a cancellation request (API)
    public function acceptCancellationRequest($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);

            if ($order->status !== 'cancel_request') {
                return response()->json(['message' => 'This order does not have a pending cancellation request'], 400);
            }

            // Vendor accepts the cancellation
            $order->update(['status' => 'cancelled']);

            ActivityLogHelper::logActivity(auth()->user(), 'Accepted cancellation request', "Order ID: {$order->id}");

            return response()->json(['message' => 'Order cancellation accepted'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to accept cancellation request', 'details' => $e->getMessage()], 500);
        }
    }

    // Vendor: Reject a cancellation request (API)
    public function rejectCancellationRequest($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);

            if ($order->status !== 'cancel_request') {
                return response()->json(['message' => 'This order does not have a pending cancellation request'], 400);
            }

            // Vendor rejects the cancellation
            $order->update(['status' => 'completed']);

            ActivityLogHelper::logActivity(auth()->user(), 'Rejected cancellation request', "Order ID: {$order->id}");

            return response()->json(['message' => 'Order cancellation rejected'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to reject cancellation request', 'details' => $e->getMessage()], 500);
        }
    }

    // User: Request cancellation of an order (API)
    public function cancelOrder($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);

            if ($order->status === 'cancelled') {
                return response()->json(['message' => 'Order is already cancelled'], 400);
            }

            if ($order->status === 'cancel_request') {
                return response()->json(['message' => 'Cancellation request already submitted'], 400);
            }

            // Request cancellation (Even if order is completed)
            $order->update(['status' => 'cancel_request']);

            ActivityLogHelper::logActivity(auth()->user(), 'Requested order cancellation', "Order ID: {$order->id}");

            return response()->json(['message' => 'Cancellation request submitted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to request cancellation', 'details' => $e->getMessage()], 500);
        }
    }

    // User: Create an order (API)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'total' => 'required|numeric|min:0',
                'cart_id' => 'required|exists:carts,id',
            ]);

            $order = Order::create([
                'user_id' => auth()->id(),
                'cart_id' => $request->cart_id,
                'total' => $request->total,
                'status' => 'pending',
            ]);

            ActivityLogHelper::logActivity(auth()->user(), 'Created an order', "Order ID: {$order->id}");

            return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create order', 'details' => $e->getMessage()], 500);
        }
    }
}
