<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
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

    // Web: List all events with filter by status (default 'active')
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $events = Event::where('status', $status)->get();
        return view('dashboard.events.index', compact('events', 'status'));
    }

    // Web: Show event details
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('dashboard.events.show', compact('event'));
    }

    // Web: Create event
    public function create()
    {
        return view('dashboard.events.create');
    }

    // Web: Store new event
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'category_id' => 'required|exists:categories,id',
        ]);

        $event = Event::create($request->all());
        $event->updateStatus();

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully');
    }

    // Web: Update event
    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'category_id' => 'required|exists:categories,id',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());
        $event->updateStatus();

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully');
    }

    // Web: Delete event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully');
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
