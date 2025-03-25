<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketOption;
use App\Models\Cart;
use Illuminate\Validation\ValidationException;
use Exception;

class CartController extends Controller
{
    // Add a ticket to the cart
    public function add(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|exists:ticket_options,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            $ticketOption = TicketOption::findOrFail($request->ticket_id);

            if ($ticketOption->quantity < $request->quantity) {
                error_log("Add to cart failed: Not enough tickets available for ticket_id {$request->ticket_id}");
                return response()->json(['error' => 'Not enough tickets available.'], 400);
            }

            $cartItem = Cart::where('user_id', $request->user()->id)
                ->where('ticket_id', $ticketOption->id)
                ->where('status', Cart::STATUS_PENDING)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
                error_log("Updated cart item for user {$request->user()->id}, ticket_id {$request->ticket_id}, new quantity: {$cartItem->quantity}, status: {$cartItem->status}");
            } else {
                $cartItem = new Cart([
                    'user_id' => $request->user()->id,
                    'ticket_id' => $ticketOption->id,
                    'quantity' => $request->quantity,
                    'event_id' => $ticketOption->event_id,
                    'status' => Cart::STATUS_PENDING,
                ]);
                error_log("Before save - New cart status: {$cartItem->status}");
                $cartItem->save();
                error_log("Added new cart item for user {$request->user()->id}, ticket_id {$request->ticket_id}, quantity: {$request->quantity}, status: {$cartItem->status}");
            }

            return response()->json(['message' => 'Ticket added to cart.', 'cart' => $cartItem], 201);
        } catch (ValidationException $e) {
            error_log("Validation error in add to cart: " . json_encode($e->errors()));
            return response()->json(['error' => 'Validation error: ' . $e->errors()], 422);
        } catch (Exception $e) {
            error_log("Error in add to cart: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // View the user's cart (only return pending carts)
    public function view(Request $request)
    {
        try {
            $cartItems = Cart::where('user_id', $request->user()->id)
                ->where('status', Cart::STATUS_PENDING)
                ->get();

            if ($cartItems->isEmpty()) {
                error_log("Cart view for user {$request->user()->id}: Cart is empty");
                return response()->json(['message' => 'Cart is empty.'], 404);
            }

            $cartItems->load('ticketOption.event');
            error_log("Cart view for user {$request->user()->id}: Retrieved " . $cartItems->count() . " pending items");

            return response()->json(['cart' => $cartItems]);
        } catch (Exception $e) {
            error_log("Error in view cart: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // Remove a ticket from the cart
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|exists:carts,ticket_id',
            ]);

            $cartItem = Cart::where('user_id', $request->user()->id)
                ->where('ticket_id', $request->ticket_id)
                ->first();

            if (!$cartItem) {
                error_log("Remove from cart failed for user {$request->user()->id}: Ticket_id {$request->ticket_id} not found in cart");
                return response()->json(['error' => 'Ticket not found in cart.'], 404);
            }

            $cartItem->delete();
            error_log("Removed cart item for user {$request->user()->id}, ticket_id {$request->ticket_id}");

            return response()->json(['message' => 'Ticket removed from cart.']);
        } catch (Exception $e) {
            error_log("Error in remove from cart: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // Update the quantity of a ticket in the cart
    public function update(Request $request)
    {
        try {
            $request->validate([
                'ticket_id' => 'required|exists:carts,ticket_id',
                'quantity' => 'required|numeric|min:1',
            ]);

            $cartItem = Cart::where('user_id', $request->user()->id)
                ->where('ticket_id', $request->ticket_id)
                ->first();

            if (!$cartItem) {
                error_log("Update cart failed for user {$request->user()->id}: Ticket_id {$request->ticket_id} not found in cart");
                return response()->json(['error' => 'Ticket not found in cart.'], 404);
            }

            $ticketOption = TicketOption::findOrFail($cartItem->ticket_id);

            if ($ticketOption->quantity < $request->quantity) {
                error_log("Update cart failed: Not enough tickets available for ticket_id {$request->ticket_id}");
                return response()->json(['error' => 'Not enough tickets available.'], 400);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            error_log("Updated cart item for user {$request->user()->id}, ticket_id {$request->ticket_id}, new quantity: {$request->quantity}");

            return response()->json(['message' => 'Cart updated.', 'cart' => $cartItem]);
        } catch (Exception $e) {
            error_log("Error in update cart: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
