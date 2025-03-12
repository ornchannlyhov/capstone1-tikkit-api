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

            $ticketOption = TicketOption::find($request->ticket_id);
            $quantity = $request->quantity;

            // Handle tickets with a refund policy
            if ($ticketOption->refund_policy) {
                // Check if cart for the same event exists
                $cart = Cart::where('user_id', $request->user()->id)
                            ->where('event_id', $ticketOption->event_id)
                            ->first();

                if ($cart) {
                    // Update quantity if cart exists
                    $cart->quantity += $quantity;
                    $cart->save();
                } else {
                    // Create a new cart if no existing cart for this event
                    Cart::create([
                        'user_id' => $request->user()->id,
                        'ticket_id' => $ticketOption->id,
                        'quantity' => $quantity,
                        'event_id' => $ticketOption->event_id,
                    ]);
                }
            } else {
                // Handle tickets with no refund policy
                $cart = Cart::where('user_id', $request->user()->id)
                            ->whereNull('event_id')
                            ->first();

                if ($cart) {
                    // Update quantity if cart exists
                    $cart->quantity += $quantity;
                    $cart->save();
                } else {
                    // Create a new cart if no existing cart for no-refund-policy tickets
                    Cart::create([
                        'user_id' => $request->user()->id,
                        'ticket_id' => $ticketOption->id,
                        'quantity' => $quantity,
                        'event_id' => null,
                    ]);
                }
            }

            return response()->json(['message' => 'Ticket added to cart.'], 201); 
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error: ' . $e->getMessage()], 422);  
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
            // Validate request
            $request->validate([
                'ticket_id' => 'required|exists:carts,ticket_id', 
            ]);

            // Find and delete the cart item
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
            // Validate request
            $request->validate([
                'ticket_id' => 'required|exists:carts,ticket_id', 
                'quantity' => 'required|numeric|min:1', 
            ]);

            // Find the cart item and update its quantity
            $cartItem = Cart::where('user_id', $request->user()->id)
                            ->where('ticket_id', $request->ticket_id)
                            ->first();

            if (!$cartItem) {
                return response()->json(['error' => 'Ticket not found in cart.'], 404);
            }

            $cartItem->quantity = $request->quantity; 
            $cartItem->save();

            return response()->json(['message' => 'Cart updated.', 'cart' => $cartItem]); 
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
