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
    // // Display all offers for a specific ticket option
    // public function index()
    // {
    //     try {

    //         $ticketOffers = TicketOffer::with('ticketOption' , 'purchasedTickets')->paginate(10);

    //         return view('dashboard.ticketOffers.index', compact('ticketOffers'));
    //     } catch (\Exception $e) {
    //         return redirect()->route('ticketOptions.index')->with('error', 'Ticket Option not found.');
    //     }
    // }

    
public function index(Request $request)
{
    try {
        $query = TicketOffer::with('ticketOption', 'purchasedTickets');

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by ticket option
        if ($request->has('ticket_option_id') && $request->ticket_option_id != '') {
            $query->where('ticket_id', $request->ticket_option_id);
        }

        // Sort by column
        if ($request->has('sort_by') && $request->sort_by != '') {
            $sortOrder = $request->get('sort_order', 'asc'); // Default to ascending order
            $query->orderBy($request->sort_by, $sortOrder);
        }

        $ticketOffers = $query->paginate(10);

        // Pass ticket options for the filter dropdown
        $ticketOptions = TicketOption::all();

        return view('dashboard.ticketOffers.index', compact('ticketOffers', 'ticketOptions'));
    } catch (\Exception $e) {
        return redirect()->route('ticketOptions.index')->with('error', 'Failed to load ticket offers.');
    }
}
    

    // Show form to create a new ticket offer
    public function create()
    {
        try {
            $ticketOptions = TicketOption::all(); 
            return view('dashboard.ticketOffers.create', compact('ticketOptions'));
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to load ticket options.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_option_id' => 'required|exists:ticket_options,id', 
            'name' => 'required|string|max:255',
            'details' => 'required|array', 
            'details.discount' => 'required|string|max:10',
            'details.valid_until' => 'required|date',
            'quantity' => 'required|numeric|min:1',
        ]);
    
        try {
            $validated['ticket_id'] = $validated['ticket_option_id'];
            unset($validated['ticket_option_id']);
    
            $validated['details'] = json_encode($validated['details']);
    
            $ticketOffer = TicketOffer::create($validated);
    
            return redirect()->route('ticketOffers.index')->with('success', 'Ticket offer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Show the form to edit a specific ticket offer
    public function edit($ticketOfferId)
    {
        try {
            $ticketOffer = TicketOffer::findOrFail($ticketOfferId);
            return view('dashboard.ticketOffers.edit', compact('ticketOffer'));
        } catch (\Exception $e) {
            return redirect()->route('ticketOffers.index')->with('error', 'Ticket offer not found.');
        }
    }

    // Update the details of a specific ticket offer
    public function update(Request $request, $ticketOfferId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'required|array',
            'details.discount' => 'required|string|max:10',
            'details.valid_until' => 'required|date',
            'quantity' => 'required|numeric|min:1',
        ]);
    
        try {
            $ticketOffer = TicketOffer::findOrFail($ticketOfferId);
    
            $validated['details'] = json_encode($validated['details']);
    
            $ticketOffer->update($validated);
    
            return redirect()->route('ticketOffers.index')->with('success', 'Ticket offer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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
