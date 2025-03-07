<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class EventController extends Controller
{
    // API: Get active events for the buyer
    public function getActiveEvents(Request $request)
    {
        try {
            $events = Event::where('status', 'active')->get();
            return response()->json([
                'success' => true,
                'message' => 'Active events retrieved successfully.',
                'data' => $events,
                'status' => 200
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching active events.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
    // API: Get events for the authenticated vendor
    public function getVendorEvents(Request $request)
    {
        try {
            $vendor = auth()->user();

            if ($vendor->role !== 'vendor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.',
                    'status' => 403
                ]);
            }

            $events = Event::where('user_id', $vendor->id)->get();

            return response()->json([
                'success' => true,
                'message' => 'Vendor events retrieved successfully.',
                'data' => $events,
                'status' => 200
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching vendor events.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    // Web: List all events with filter by status (default 'active')
    public function index(Request $request)
    {
        try {
            $status = $request->query('status', 'active');
            $events = Event::where('status', $status)->get();
            return view('dashboard.events.index', compact('events', 'status'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching events.');
        }
    }

    // Web: Show event details
    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            return view('dashboard.events.show', compact('event'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.events.index')->with('error', 'Event not found.');
        } catch (Exception $e) {
            return redirect()->route('admin.events.index')->with('error', 'Error loading event details.');
        }
    }

    // Web: Create event
    public function create()
    {
        return view('dashboard.events.create');
    }

    // Web: Store new event
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'category_id' => 'required|exists:categories,id',
            ]);

            $event = Event::create($validated);
            $event->updateStatus();

            return redirect()->route('admin.events.index')->with('success', 'Event created successfully');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create event.')->withInput();
        }
    }

    // Web: Update event
    public function update($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'category_id' => 'required|exists:categories,id',
            ]);

            $event = Event::findOrFail($id);
            $event->update($validated);
            $event->updateStatus();

            return redirect()->route('admin.events.index')->with('success', 'Event updated successfully');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.events.index')->with('error', 'Event not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update event.')->withInput();
        }
    }

    // Web: Delete event
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();
            return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.events.index')->with('error', 'Event not found.');
        } catch (Exception $e) {
            return redirect()->route('admin.events.index')->with('error', 'Failed to delete event.');
        }
    }

    // API: Toggle the event status manually (public/unpublic)
    public function togglePublic($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->status = $event->status === 'active' ? 'upcoming' : 'active';
            $event->save();

            return response()->json([
                'success' => true,
                'message' => "Event status updated to {$event->status}",
                'data' => $event,
                'status' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
                'status' => 404
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating event status.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
}
