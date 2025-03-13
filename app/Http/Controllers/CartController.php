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

            // Check if there's enough available quantity
            if ($ticketOption->available_quantity < $request->quantity) {
                return response()->json(['error' => 'Not enough tickets available.'], 400);
            }

            // Find existing cart item
            $cartItem = Cart::where('user_id', $request->user()->id)
                ->where('ticket_id', $ticketOption->id)
                ->first();

            if ($cartItem) {
                // Update quantity
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $request->user()->id,
                    'ticket_id' => $ticketOption->id,
                    'quantity' => $request->quantity,
                    'event_id' => $ticketOption->event_id, //Add this incase it needed
                ]);
            }

            return response()->json(['message' => 'Ticket added to cart.', 'cart' => $cartItem], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error: ' . $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // View the user's cart
    public function view(Request $request)
    {
        try {
            $cartItems = Cart::where('user_id', $request->user()->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Cart is empty.'], 404);
            }

            $cartItems->load('ticketOption.event');

            return response()->json(['cart' => $cartItems]);
        } catch (Exception $e) {
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
                return response()->json(['error' => 'Ticket not found in cart.'], 404);
            }

            $cartItem->delete();

            return response()->json(['message' => 'Ticket removed from cart.']);
        } catch (Exception $e) {
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
                return response()->json(['error' => 'Ticket not found in cart.'], 404);
            }
            $ticketOption = TicketOption::findOrFail($cartItem->ticket_id);
            // Check if there's enough available quantity
            if ($ticketOption->available_quantity < $request->quantity) {
                return response()->json(['error' => 'Not enough tickets available.'], 400);
            }
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json(['message' => 'Cart updated.', 'cart' => $cartItem]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}