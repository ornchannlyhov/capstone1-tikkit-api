<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class EventController extends Controller
{
    // API: Get events for the buyer
    public function getEvents(Request $request)
    {
        try {
            $status = $request->query('status', null);

            $eventsQuery = Event::with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone_number')
                        ->where('role', 'vendor'); 
                }
            ]);

            if ($status && in_array($status, ['upcoming', 'active', 'passed', 'delay'])) {
                $eventsQuery->where('status', $status);
            }

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
            $vendor = $request->user();

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

    // Web: List all events with search
    public function index(Request $request)
    {
        try {
            $query = Event::with(['user', 'category']); 
    
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('user', function ($q) use ($searchTerm) {
                          $q->where('name', 'LIKE', "%{$searchTerm}%");
                      });
            }
    
            $events = $query->paginate(10);
    
            return view('dashboard.events.index', compact('events'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching events.');
        }
    }
    

    // Web: Create event
    public function create()
    {
        //fecth all categories
        $categories = \App\Models\Category::all(); 
        
        return view('dashboard.events.create', compact('categories'));
    }
    
     // Web: Store event
    public function store(Request $request)
    {
       
        //get user id
        $user_id = auth()->id();
        
        if (!$user_id) {
            return redirect()->back()->withErrors('User not authenticated. Please log in.');
        }
    
        $request->validate([
            'name' => 'required|string|max:191',
            'category_id' => 'nullable|integer|exists:categories,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:upcoming,active,passed,delay',
        ]);
        $request->merge(['user_id' => $user_id]);
    
        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->move(public_path('images'), $request->file('image')->getClientOriginalName());
            $imagePath = 'images/' . $request->file('image')->getClientOriginalName();
        }
    
        // Create event
        Event::create([
            'user_id' => $user_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'status' => $request->status,
        ]);
    
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }
    
    // Web: show event
    public function show($id)
    {
        $event = Event::findOrFail($id);
        //fetch all caterogies
        $categories = \App\Models\Category::all(); 
        
        return view('dashboard.events.edit', compact('event','categories'));
    }
    // Web: update event
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'description' => 'nullable|string',
            'status' => 'required|in:upcoming,active,passed,delay',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = null;

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($event->image && file_exists(public_path($event->image))) {
                unlink(public_path($event->image));
            }
    
            // Store new image
            $path = $request->file('image')->move(public_path('images'), $request->file('image')->getClientOriginalName());
            $event->image = 'images/' . $request->file('image')->getClientOriginalName();
        }

        // Update event details
        $event->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }
    // Web: delete event
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Delete the image file if it exists
            if ($event->image && file_exists(public_path($event->image))) {
                unlink(public_path($event->image));
            }
            // Delete the event
            $event->delete();

            return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')->with('error', 'Event not found.');
        } catch (Exception $e) {
            return redirect()->route('events.index')->with('error', 'Error deleting event.');
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