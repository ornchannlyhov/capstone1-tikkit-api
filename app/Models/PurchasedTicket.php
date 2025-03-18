<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'offer_id',
        'discount_amount',
        'qr_code',
        'status',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketOption::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(TicketOffer::class, 'offer_id');
    }
}
