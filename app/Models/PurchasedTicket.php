<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedTicket extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'qr_code', 'status'];

    public function ticketOption()
    {
        return $this->belongsTo(TicketOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

