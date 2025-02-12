<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Event;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Display a listing of addresses.
    public function index()
    {
        $addresses = Address::all();
        return view('addresses.index', compact('addresses'));
    }

    //Show the form for creating a new address.

    public function create()
    {
        $events = Event::all();
        return view('addresses.create', compact('events'));
    }

    // Store a newly created address.
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'extra_info' => 'nullable|string',
        ]);

        Address::create($request->all());

        return redirect()->route('addresses.index')->with('success', 'Address created successfully.');
    }

    // Display the specified address.
    public function show($id)
    {
        $address = Address::findOrFail($id);
        return view('addresses.show', compact('address'));
    }

    // Show the form for editing an address.
    public function edit($id)
    {
        $address = Address::findOrFail($id);
        $events = Event::all();
        return view('addresses.edit', compact('address', 'events'));
    }

    // Update the specified address.
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $request->validate([
            'event_id' => 'sometimes|exists:events,id',
            'street' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'extra_info' => 'nullable|string',
        ]);

        $address->update($request->all());

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
    }

    //Remove the specified address.
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();

        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully.');
    }


    // Assign an address to an event or revoke it.
    public function toggleEventAssignment(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        $request->validate([
            'event_id' => 'nullable|exists:events,id',
        ]);
        if ($request->event_id) {
            $address->event_id = $request->event_id;
            $message = 'Address assigned to event successfully.';
        } else {
            if ($address->event_id) {
                $address->event_id = null;
                $message = 'Address revoked from event.';
            } else {
                $message = 'Address is already unassigned.';
            }
        }
        $address->save();
        return redirect()->route('addresses.index')->with('success', $message);
    }

}
