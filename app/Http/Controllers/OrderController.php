<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Admin: View all orders (web)
    public function index()
    {
        $orders = Order::with('user')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Admin: View a specific order (web)
    public function show($id)
    {
        $order = Order::with(['user', 'carts'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Admin: View all cancellation requests (API)
    public function viewCancellationRequests()
    {
        try {
            $cancellationRequests = Order::where('status', 'cancel_request')->get();
            return view('admin.orders.cancellation_requests', compact('cancellationRequests'));
        } catch (Exception $e) {
            return view('admin.orders.error', ['error' => 'Failed to retrieve cancellation requests', 'details' => $e->getMessage()]);
        }
    }

    // Vendor: View all vendor's orders (API)
    public function vendorIndex()
    {
        $user = Auth::user();
        if ($user->role === 'vendor') {
            // Get all orders related to events created by this vendor
            $orders = Order::whereHas('carts.ticketOption.event', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            // Load related models to avoid N+1 issues
            $orders->load('carts.ticketOption.event', 'user');

            return response()->json(['orders' => $orders], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Vendor: View a specific vendor's order (API)
    public function vendorShow($id)
    {
        $user = Auth::user();
        if ($user->role === 'vendor') {
            $order = Order::where('id', $id)
                ->whereHas('carts.ticketOption.event', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['carts.ticketOption.event', 'user'])
                ->firstOrFail();

            return response()->json(['order' => $order], 200);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Vendor: Accept a cancellation request (API)
    public function acceptCancellationRequest($id)
    {
        $user = Auth::user();
        if ($user->role === 'vendor') {
            try {
                $order = Order::where('id', $id)
                    ->whereHas('carts.ticketOption.event', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->firstOrFail();

                if ($order->status !== 'cancel_request') {
                    return response()->json(['message' => 'This order does not have a pending cancellation request'], 400);
                }

                $order->update(['status' => 'cancelled']);

                ActivityLogHelper::logActivity($user, 'Accepted cancellation request', "Order ID: {$order->id}");

                return response()->json(['message' => 'Order cancellation accepted'], 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to accept cancellation request', 'details' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Vendor: Reject a cancellation request (API)
    public function rejectCancellationRequest($id)
    {
        $user = Auth::user();
        if ($user->role === 'vendor') {
            try {
                $order = Order::where('id', $id)
                    ->whereHas('carts.ticketOption.event', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->firstOrFail();

                if ($order->status !== 'cancel_request') {
                    return response()->json(['message' => 'This order does not have a pending cancellation request'], 400);
                }

                $order->update(['status' => 'completed']);

                ActivityLogHelper::logActivity($user, 'Rejected cancellation request', "Order ID: {$order->id}");

                return response()->json(['message' => 'Order cancellation rejected'], 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to reject cancellation request', 'details' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // User: View all their orders (API)
    public function userOrders()
    {
        try {
            $orders = Order::where('user_id', auth()->id())->with('carts.ticketOption.event')->get();
            return response()->json(['orders' => $orders], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve orders', 'details' => $e->getMessage()], 500);
        }
    }

    // User: Request cancellation of an order (API)
    public function cancelOrder($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);

            if (in_array($order->status, ['cancelled', 'cancel_request'])) {
                return response()->json(['message' => 'Cancellation request already submitted or order is cancelled'], 400);
            }

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
                'cart_ids' => 'required|array',
                'cart_ids.*' => 'exists:carts,id',
            ]);

            // Get the carts from the database
            $carts = \App\Models\Cart::whereIn('id', $request->cart_ids)->where('user_id', auth()->id())->get();

            if ($carts->count() !== count($request->cart_ids)) {
                return response()->json(['error' => 'One or more carts are invalid or do not belong to the user.'], 400);
            }

            // Start database transaction for atomicity
            DB::beginTransaction();

            try {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'total' => $request->total,
                    'status' => 'pending',
                ]);

                $order->carts()->attach($request->cart_ids);

                // Delete the carts after attaching them to the order
                \App\Models\Cart::destroy($request->cart_ids);

                DB::commit();

                ActivityLogHelper::logActivity(auth()->user(), 'Created an order', "Order ID: {$order->id}");

                return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create order', 'details' => $e->getMessage()], 500);
        }
    }
}