<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\TicketOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketOptionController extends Controller
{
    // Vendor: View only their own ticket options
    public function vendorIndex($eventId)
    {
        try {
            $vendorId = auth()->id();

            // Ensure the vendor owns this event
            $event = Event::where('id', $eventId)
                ->where('user_id', $vendorId)
                ->firstOrFail();

            // Fetch ticket options along with their offers (merch items)
            $ticketOptions = TicketOption::where('event_id', $event->id)
                ->with('ticketOffers')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ticket options retrieved successfully.',
                'data' => [
                    'event' => $event,
                    'ticketOptions' => $ticketOptions
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view these ticket options.',
                'error' => $e->getMessage()
            ], 403);
        }
    }
    // Get all tickets (TicketOptions) for a specific event
    public function getEventTickets($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $ticketOptions = $event->ticketOptions()->with('ticketOffers')->get();

            return response()->json([
                'success' => true,
                'data' => $ticketOptions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ticket options: ' . $e->getMessage()
            ], 500);
        }
    }


    public function index(Request $request)
    {
        $query = TicketOption::query();

        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('type', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $ticketOptions = $query->paginate(10);
        $events = Event::all();

        $event = $events->first();

        return view('dashboard.ticketOptions.index', compact('ticketOptions', 'events', 'event' , 'sortBy', 'sortOrder'));
    }

    // Show the form for creating a new TicketOption for an event
    public function create()
    {
        try {
            $events = Event::all();
            return view('dashboard.ticketOptions.create', compact('events'));
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Event not found or error fetching event details.');
        }
    }

    // Store a newly created TicketOption for an event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'is_active' => 'required|boolean',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'description' => 'nullable|string',
            'refund_policy' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            $ticketOption = TicketOption::create($validated);

    
            if ($request->hasFile('image')) {
                $ticketOption->image = $request->file('image')->store('ticket-options', 'public');
            }
    
            $ticketOption->save();
    
            return redirect()->route('ticketOptions.index')->with('success', 'Ticket Option created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to create Ticket Option.');
        }
    }

    // Show the form for editing a specific TicketOption
    public function edit($id)
    {
        try {
            $ticketOption = TicketOption::findOrFail($id);
            $events = Event::all();
            return view('dashboard.ticketOptions.edit', compact('ticketOption' , 'events'));
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Ticket Option not found.');
        }
    }

    // Update a specific TicketOption
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'is_active' => 'required|boolean',
            'refund_policy' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'event_id' => 'required|exists:events,id',
        ]);
    
        try {
            $ticketOption = TicketOption::findOrFail($id);
    
            if ($request->hasFile('image')) {
                if ($ticketOption->image) {
                    Storage::disk('public')->delete($ticketOption->image);
                }
                $imagePath = $request->file('image')->store('ticket-options', 'public');
                $ticketOption->image = $imagePath;
            }
    
            $ticketOption->update([
                'type' => $request->type,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'is_active' => $request->is_active,
                'refund_policy' => $request->refund_policy,
                'description' => $request->description,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
                'event_id' => $request->event_id,
            ]);
    
            return redirect()->route('ticketOptions.index')->with('success', 'Ticket Option updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to update Ticket Option: ' . $e->getMessage());
        }
    }

    // Delete a TicketOption
    public function destroy($id)
    {
        try {
            $ticketOption = TicketOption::findOrFail($id);
            if ($ticketOption->image) {
                Storage::disk('public')->delete($ticketOption->image);
            }
            $ticketOption->delete();
            return redirect()->route('ticketOptions.index', $ticketOption->event_id)->with('success', 'Ticket Option deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to delete Ticket Option: ' . $e->getMessage());
        }
    }
}
