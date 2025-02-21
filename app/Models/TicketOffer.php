<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 
        'name', 
        'details', 
        'quantity', 
    ];

    public function ticketOption()
    {
        return $this->belongsTo(TicketOption::class, 'ticket_id');
    }
}
