<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class AddressController extends Controller
{
    // Display a listing of addresses.
    public function index()
    {
        try {
            $addresses = Address::all();
            return view('dashboard.addresses.index', compact('addresses'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching addresses.');
        }
    }

    // Show the form for creating a new address.
    public function create()
    {
        try {
            $events = Event::all();
            return view('dashboard.addresses.create', compact('events'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error loading create address form.');
        }
    }

    // Store a newly created address.
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,id',
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'venue_name' => 'nullable|string|max:255',
                'extra_info' => 'nullable|string',
            ]);

            Address::create($validated);

            return redirect()->route('addresses.index')->with('success', 'Address created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create address.')->withInput();
        }
    }

    // Display the specified address.
    public function show($id)
    {
        try {
            $address = Address::findOrFail($id);
            return view('dashboard.addresses.show', compact('address'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('addresses.index')->with('error', 'Address not found.');
        } catch (Exception $e) {
            return redirect()->route('addresses.index')->with('error', 'Error loading address details.');
        }
    }

    // Show the form for editing an address.
    public function edit($id)
    {
        try {
            $address = Address::findOrFail($id);
            $events = Event::all();
            return view('dashboard.addresses.edit', compact('address', 'events'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('addresses.index')->with('error', 'Address not found.');
        } catch (Exception $e) {
            return redirect()->route('addresses.index')->with('error', 'Error loading edit form.');
        }
    }

    // Update the specified address.
    public function update(Request $request, $id)
    {
        try {
            $address = Address::findOrFail($id);

            $validated = $request->validate([
                'event_id' => 'sometimes|exists:events,id',
                'street' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:255',
                'country' => 'sometimes|string|max:255',
                'venue_name' => 'nullable|string|max:255',
                'extra_info' => 'nullable|string',
            ]);

            $address->update($validated);

            return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('addresses.index')->with('error', 'Address not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update address.')->withInput();
        }
    }

    // Remove the specified address.
    public function destroy($id)
    {
        try {
            $address = Address::findOrFail($id);
            $address->delete();

            return redirect()->route('addresses.index')->with('success', 'Address deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('addresses.index')->with('error', 'Address not found.');
        } catch (Exception $e) {
            return redirect()->route('addresses.index')->with('error', 'Failed to delete address.');
        }
    }

    // Assign an address to an event or revoke it.
    public function toggleEventAssignment(Request $request, $id)
    {
        try {
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
        } catch (ModelNotFoundException $e) {
            return redirect()->route('addresses.index')->with('error', 'Address not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return redirect()->route('addresses.index')->with('error', 'Failed to update event assignment.');
        }
    }
}
