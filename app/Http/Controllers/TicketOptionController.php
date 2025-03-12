<?php

namespace App\Http\Controllers;

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
            $event = Event::where('id', $eventId)->where('user_id', $vendorId)->firstOrFail();
            $ticketOptions = TicketOption::where('event_id', $event->id)->get();
            return view('dashboard.ticketOptions.vendor_index', compact('ticketOptions', 'event'));
        } catch (\Exception $e) {
            return redirect()->route('events.index')->with('error', 'You do not have permission to view these ticket options.');
        }
    }
    // Get all tickets (TicketOptions) for a specific event
    public function getEventTickets($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $ticketOptions = $event->ticketOptions;
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
    // Show all TicketOptions for an event
    public function index($id)
    {
        try {
            $ticketOptions = TicketOption::where('event_id', $id)->get();
            return view('dashboard.ticketOptions.index', compact('ticketOptions', 'id'));
        } catch (\Exception $e) {
            return redirect()->route('events.index')->with('error', 'Failed to fetch ticket options.');
        }
    }

    // Show the form for creating a new TicketOption for an event
    public function create($id)
    {
        try {
            $event = Event::findOrFail($id);
            return view('dashboard.ticketOptions.create', compact('id', 'event'));
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index', $id)->with('error', 'Event not found or error fetching event details.');
        }
    }

    // Store a newly created TicketOption for an event
    public function store(Request $request, $id)
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
        ]);

        try {
            $event = Event::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('ticket_options', 'public');
            }

            TicketOption::create([
                'event_id' => $id,
                'type' => $request->type,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'is_active' => $request->is_active,
                'refund_policy' => $request->refund_policy,
                'description' => $request->description,
                'image' => $imagePath,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);

            return redirect()->route('ticketOptions.index', $id)->with('success', 'Ticket Option created successfully');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index', $id)->with('error', 'Failed to create Ticket Option: ' . $e->getMessage());
        }
    }

    // Show the form for editing a specific TicketOption
    public function edit($id)
    {
        try {
            $ticketOption = TicketOption::findOrFail($id);
            return view('dashboard.ticketOptions.edit', compact('ticketOption'));
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
        ]);

        try {
            $ticketOption = TicketOption::findOrFail($id);
            $event = Event::findOrFail($ticketOption->event_id);

            if ($request->hasFile('image')) {
                if ($ticketOption->image) {
                    Storage::disk('public')->delete($ticketOption->image);
                }
                $imagePath = $request->file('image')->store('ticket_options', 'public');
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
            ]);

            return redirect()->route('ticketOptions.index', $ticketOption->event_id)->with('success', 'Ticket Option updated successfully');
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
