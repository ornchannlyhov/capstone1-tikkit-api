<?php
namespace App\Http\Controllers;

use App\Models\TicketOption;
use App\Models\TicketOffer;
use Illuminate\Http\Request;

class TicketOfferController extends Controller
{
    // Vendor: View only their own ticket offers
    public function vendorIndex($ticketOptionId)
    {
        try {
            $vendorId = auth()->id();
            $ticketOption = TicketOption::whereHas('event', function ($query) use ($vendorId) {
                $query->where('user_id', $vendorId);
            })->findOrFail($ticketOptionId);
            $ticketOffers = $ticketOption->ticketOffers()->paginate(10);

            return response()->json([
                'success' => true,
                'ticketOffers' => $ticketOffers,
                'ticketOption' => $ticketOption,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view these ticket offers.',
            ], 403); 
        }
    }
    // Display all offers for a specific ticket option
    public function index($ticketOptionId)
    {
        try {
            $ticketOption = TicketOption::findOrFail($ticketOptionId);
            $ticketOffers = $ticketOption->ticketOffers()->paginate(10);
            return view('dashboard.ticketOffers.index', compact('ticketOffers', 'ticketOption'));  // Updated path
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Ticket Option not found.');
        }
    }

    // Show form to create a new ticket offer
    public function create($ticketOptionId)
    {
        try {
            $ticketOption = TicketOption::findOrFail($ticketOptionId);
            return view('dashboard.ticketOffers.create', compact('ticketOption'));  // Updated path
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Ticket Option not found.');
        }
    }

    // Store a new ticket offer
    public function store(Request $request, $ticketOptionId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $ticketOption = TicketOption::findOrFail($ticketOptionId);

            $ticketOffer = new TicketOffer([
                'name' => $request->name,
                'details' => $request->details,
                'quantity' => $request->quantity,
            ]);

            $ticketOption->ticketOffers()->save($ticketOffer);

            return redirect()->route('ticketOffers.index', $ticketOptionId)->with('success', 'Ticket offer created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to create ticket offer.');
        }
    }

    // Show the form to edit a specific ticket offer
    public function edit($ticketOfferId)
    {
        try {
            $ticketOffer = TicketOffer::findOrFail($ticketOfferId);
            return view('dashboard.ticketOffers.edit', compact('ticketOffer'));  // Updated path
        } catch (\Exception $e) {
            return redirect()->route('ticketOffers.index', $ticketOffer->ticket_id)->with('error', 'Ticket offer not found.');
        }
    }

    // Update the details of a specific ticket offer
    public function update(Request $request, $ticketOfferId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $ticketOffer = TicketOffer::findOrFail($ticketOfferId);
            $ticketOffer->update([
                'name' => $request->name,
                'details' => $request->details,
                'quantity' => $request->quantity,
            ]);

            return redirect()->route('ticketOffers.index', $ticketOffer->ticket_id)->with('success', 'Ticket offer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('ticketOffers.index')->with('error', 'Failed to update ticket offer.');
        }
    }

    // Delete a ticket offer
    public function destroy($ticketOfferId)
    {
        try {
            $ticketOffer = TicketOffer::findOrFail($ticketOfferId);
            $ticketOffer->delete();

            return redirect()->route('ticketOffers.index', $ticketOffer->ticket_id)->with('success', 'Ticket offer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('ticketOffers.index')->with('error', 'Failed to delete ticket offer.');
        }
    }
}
