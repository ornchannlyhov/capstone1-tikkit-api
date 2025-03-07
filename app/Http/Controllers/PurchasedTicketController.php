<?php
namespace App\Http\Controllers;

use App\Models\PurchasedTicket;
use App\Models\TicketOption;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchasedTicketController extends Controller
{
    // Store a purchased ticket (called after payment success)
    public function store(Request $request)
    {
        $request->validate([
            'ticket_option_id' => 'required|exists:ticket_options,id',
        ]);

        $ticketOption = TicketOption::findOrFail($request->ticket_option_id);
        if ($ticketOption->quantity <= 0) {
            return response()->json(['error' => 'Sold out'], 400);
        }

        $ticketOption->reduceQuantity(1);
        $uniqueHash = Str::uuid()->toString();

        $purchasedTicket = PurchasedTicket::create([
            'ticket_id' => $ticketOption->id,
            'user_id' => auth()->id(),
            'qr_code' => $uniqueHash,
            'status' => 'valid',
        ]);

        // Generate QR code image (base64 encoded PNG)
        $qrCode = base64_encode(QrCode::format('png')->size(300)->generate(json_encode([
            'ticket_id' => $purchasedTicket->id,
            'hash' => $uniqueHash,
        ])));

        return response()->json([
            'ticket_id' => $purchasedTicket->id,
            'qr_code' => $qrCode,
        ]);
    }

    // Buyer: View purchased tickets
    public function viewPurchasedTicketsForBuyer(Request $request)
    {
        $purchasedTickets = PurchasedTicket::where('user_id', auth()->id())
            ->with('ticketOption')
            ->get();

        return response()->json([
            'tickets' => $purchasedTickets,
        ]);
    }

    // Vendor: View purchased tickets for their events
    public function viewPurchasedTicketsForVendor()
    {
        $vendorId = auth()->id();

        // Get tickets only for events owned by the vendor
        $tickets = PurchasedTicket::whereHas('ticketOption.event', function ($query) use ($vendorId) {
            $query->where('user_id', $vendorId);
        })->with('ticketOption.event')->get();

        return response()->json([
            'tickets' => $tickets,
        ]);
    }

    // Validate QR code (vendor function)
    public function validateQR(Request $request)
    {
        $data = json_decode($request->qr_data, true);

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

        return response()->json(['status' => 'success', 'message' => 'Ticket Validated']);
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
            return redirect()->route('dashboard.ticketOptions.index')->with('error', 'Failed to load purchased tickets.');
        }
    }
}
