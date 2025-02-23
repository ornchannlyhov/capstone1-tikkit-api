<?php

namespace App\Http\Controllers;

use App\Models\TicketOption;
use Illuminate\Http\Request;

class TicketOptionController extends Controller
{
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
            return view('dashboard.ticketOptions.create', compact('id'));
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
        ]);

        try {
            // Create the new TicketOption for the event
            TicketOption::create([
                'event_id' => $id,
                'type' => $request->type,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'is_active' => $request->is_active,
                'refund_policy' => $request->refund_policy,
                'description' => $request->description,
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
        ]);

        try {
            $ticketOption = TicketOption::findOrFail($id);

            // Update the TicketOption
            $ticketOption->update([
                'type' => $request->type,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'is_active' => $request->is_active,
                'refund_policy' => $request->refund_policy,
                'description' => $request->description,
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

            // Delete the TicketOption
            $ticketOption->delete();

            return redirect()->route('ticketOptions.index', $ticketOption->event_id)->with('success', 'Ticket Option deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('ticketOptions.index')->with('error', 'Failed to delete Ticket Option: ' . $e->getMessage());
        }
    }
}
