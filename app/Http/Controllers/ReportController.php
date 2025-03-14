<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PurchasedTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Admin: Get event statistics and return a view
    public function adminEventStats()
    {
        $activeEventsCount = Event::where('status', 'active')->count();
        $upcomingEventsCount = Event::where('status', 'upcoming')->count();
        $passedEventsCount = Event::where('status', 'passed')->count();
        $delayedEventsCount = Event::where('status', 'delay')->count();

        // Events due to start within the next week
        $dueDate = Carbon::now()->addWeek();
        $dueEvents = Event::where('startDate', '<=', $dueDate)
            ->where('status', 'upcoming')
            ->count();

        return view('dashboard.reports.event_stats', [
            'activeEventsCount' => $activeEventsCount,
            'upcomingEventsCount' => $upcomingEventsCount,
            'passedEventsCount' => $passedEventsCount,
            'delayedEventsCount' => $delayedEventsCount,
            'dueEventsCount' => $dueEvents,
        ]);
    }

    // Vendor: Get ticket sales report (API)
    public function vendorTicketSalesReport(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'vendor') {
            return response()->json(['error' => 'Unauthorized. Vendor access required.'], 403);
        }

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PurchasedTicket::whereHas('ticketOption.event', function ($q) use ($user) {
            $q->where('user_id', $user->id); // Ensure the event belongs to the vendor
        });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $totalTicketsSold = $query->count();

        // Calculate total revenue
        $totalRevenue = $query->join('ticket_options', 'purchased_tickets.ticket_id', '=', 'ticket_options.id')
            ->sum('ticket_options.price'); // Sum the ticket prices

        return response()->json([
            'total_tickets_sold' => $totalTicketsSold,
            'total_revenue' => $totalRevenue,
        ], 200);
    }
}