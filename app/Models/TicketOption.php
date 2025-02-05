<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOption extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'type', 'refund_policy', 'description'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketVariants()
    {
        return $this->hasMany(TicketVariant::class);
    }
}
