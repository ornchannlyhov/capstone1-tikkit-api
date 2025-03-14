<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'refund_policy',
        'description',
        'image',
        'price',
        'quantity',
        'startDate',
        'endDate',
        'is_active',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketOffers()
    {
        return $this->hasMany(TicketOffer::class);
    }

    public function purchasedTickets()
    {
        return $this->hasMany(PurchasedTicket::class);
    }
}
