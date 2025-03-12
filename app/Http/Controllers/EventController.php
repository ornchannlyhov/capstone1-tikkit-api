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
    public function getEvents(Request $request)
    {
        try {
            $status = $request->query('status', null);
            $eventsQuery = Event::query();
            if ($status && in_array($status, ['upcoming', 'active', 'passed', 'delay'])) {
                // Filter events by status if status is valid
                $eventsQuery->where('status', $status);
            }
            // Fetch the events
            $events = $eventsQuery->get();

            return response()->json([
                'success' => true,
                'message' => 'Events retrieved successfully.',
                'data' => $events,
                'status' => 200
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching events.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    // API: Get events filtered by category
    public function getEventsByCategory(Request $request)
    {
        try {
            $categoryId = $request->query('category_id', null);

            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required.',
                    'status' => 400
                ]);
            }

            // Fetch the events for the specified category
            $events = Event::where('category_id', $categoryId)->get();

            // Check if events exist for the given category
            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found for this category.',
                    'status' => 404
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Events retrieved successfully.',
                'data' => $events,
                'status' => 200
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching events.',
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

    // Web: List all events with no status filter by default
    public function index(Request $request)
    {
        try {
            $status = $request->query('status', null);
            $eventsQuery = Event::query();
            if ($status) {
                $eventsQuery->where('status', $status);
            }
            $events = $eventsQuery->get();
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
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'category_id' => 'required|exists:categories,id',
                'status' => 'nullable|string|in:active,upcoming,completed'
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('events', 'public');
            }

            $event = Event::create($validated);

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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'category_id' => 'required|exists:categories,id',
                'status' => 'nullable|string|in:active,upcoming,completed'
            ]);

            $event = Event::findOrFail($id);

            // Handle image upload if a new one is provided
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('events', 'public');
            }

            $event->update($validated);

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

    // API/Web: Search events based on name or description
    public function search(Request $request)
    {
        try {
            $query = $request->query('search');
            $events = Event::where('name', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->get();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Search results retrieved successfully.',
                    'data' => $events,
                    'status' => 200
                ]);
            } else {
                return view('dashboard.events.index', compact('events'));
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching events.',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }
}
