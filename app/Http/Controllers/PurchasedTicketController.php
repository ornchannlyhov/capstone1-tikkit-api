<?php

namespace App\Http\Controllers;

use App\Models\PurchasedTicket;
use App\Models\TicketOffer;
use App\Models\TicketOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchasedTicketController extends Controller
{
    // Store a purchased ticket (called after payment success)
    public function store(Request $request)
    {
        $request->validate([
            'ticket_option_id' => 'required|exists:ticket_options,id',
            'offer_id' => 'nullable|exists:offers,id',
        ]);

        $ticketOption = TicketOption::findOrFail($request->ticket_option_id);

        if ($ticketOption->available_quantity <= 0) {
            return response()->json(['error' => 'Sold out'], 400);
        }

        $offer = null;
        if ($request->has('offer_id')) {
            $offer = TicketOffer::active()->find($request->offer_id);

            if (!$offer) {
                return response()->json(['error' => 'Invalid or expired offer'], 400);
            }

            if ($offer->usage_limit !== null && $offer->usage_limit <= 0) {
                return response()->json(['error' => 'Offer usage limit reached'], 400);
            }
        }

        DB::beginTransaction();
        try {
            $ticketOption->decrement('quantity', 1);

            if ($offer && $offer->usage_limit !== null) {
                $offer->decrement('usage_limit', 1);
            }

            $uniqueHash = Str::uuid()->toString();

            $purchasedTicket = PurchasedTicket::create([
                'ticket_id' => $ticketOption->id,
                'user_id' => auth()->id(),
                'offer_id' => $offer ? $offer->id : null,
                'qr_code' => $uniqueHash,
                'status' => 'valid',
            ]);

            $qrCode = base64_encode(QrCode::format('png')->size(300)->generate(json_encode([
                'ticket_id' => $purchasedTicket->id,
                'hash' => $uniqueHash,
            ])));

            DB::commit();

            return response()->json([
                'ticket_id' => $purchasedTicket->id,
                'qr_code' => $qrCode,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error purchasing ticket: " . $e->getMessage());
            return response()->json(['error' => 'Failed to process purchase'], 500);
        }
    }

    // Buyer: View purchased tickets
    public function viewPurchasedTicketsForBuyer(Request $request)
    {
        try {
            $purchasedTickets = PurchasedTicket::where('user_id', auth()->id())
                ->with(['ticketOption.event', 'offer']) 
                ->get();

            // Format the response
            $response = $purchasedTickets->map(function ($ticket) {
                return [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->ticketOption->type,
                    'ticket_price' => $ticket->ticketOption->price,
                    'event' => [
                        'event_name' => $ticket->ticketOption->event->name,
                        'event_date' => $ticket->ticketOption->event->created_at, 
                    ],
                    'qr_code' => $ticket->qr_code,
                    'status' => $ticket->status,
                    'offer' => $ticket->offer ? [
                        'offer_description' => $ticket->offer->description,
                        'offer_discount' => $ticket->offer->discount_percentage . '%',
                        'offer_start_date' => $ticket->offer->start_date,
                        'offer_end_date' => $ticket->offer->end_date,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'tickets' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchased tickets: ' . $e->getMessage(),
            ], 500);
        }
    }


    // Vendor: View purchased tickets for their events
    public function viewPurchasedTicketsForVendor()
    {
        try {
            $vendorId = auth()->id();

            $tickets = PurchasedTicket::whereHas('ticketOption.event', function ($query) use ($vendorId) {
                $query->where('user_id', $vendorId);
            })
                ->with(['ticketOption.event', 'offer'])
                ->get();

            // Format the response
            $response = $tickets->map(function ($ticket) {
                return [
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->ticketOption->name,
                    'ticket_price' => $ticket->ticketOption->price,
                    'event' => [
                        'event_name' => $ticket->ticketOption->event->name,
                        'event_date' => $ticket->ticketOption->event->created_at, 
                    ],
                    'qr_code' => $ticket->qr_code,
                    'status' => $ticket->status,
                    'offer' => $ticket->offer ? [
                        'offer_description' => $ticket->offer->description,
                        'offer_discount' => $ticket->offer->discount_percentage . '%',
                        'offer_start_date' => $ticket->offer->start_date,
                        'offer_end_date' => $ticket->offer->end_date,
                    ] : null, 
                ];
            });

            return response()->json([
                'success' => true,
                'tickets' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchased tickets: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Admin: View purchased tickets for a specific ticket option
    public function viewPurchasedTicketsForAdmin($ticketOptionId)
    {
        try {
            $tickets = PurchasedTicket::where('ticket_id', $ticketOptionId)
                ->with('ticketOption')
                ->get();
            return view('dashboard.purchased_tickets.index', compact('tickets'));
        } catch (\Exception $e) {
            Log::error("Error viewing purchased tickets for admin: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard.ticketOptions.index')->with('error', 'Failed to load purchased tickets.');
        }
    }

    // Validate QR code (vendor function)
    public function validateQR(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|json'
        ]);
        $data = json_decode($request->qr_data, true);

        if (!isset($data['ticket_id']) || !isset($data['hash'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid QR Code Data'], 400);
        }
        $ticket = PurchasedTicket::where('id', $data['ticket_id'])
            ->where('qr_code', $data['hash'])
            ->first();

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Invalid QR Code'], 404);
        }
        if ($ticket->status !== 'valid') {
            return response()->json(['status' => 'error', 'message' => 'Ticket Already Used'], 400);
        }

        $ticket->update(['status' => 'used']);

        return response()->json(['status' => 'success', 'message' => 'Ticket Validated'], 200);
    }

}