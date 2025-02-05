<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketVariant extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'name', 'description', 'price', 'quantity', 'status'];

    public function ticketOption()
    {
        return $this->belongsTo(TicketOption::class, 'ticket_id');
    }
}
