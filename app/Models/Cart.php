<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'variant_id', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketVariant()
    {
        return $this->belongsTo(TicketVariant::class, 'variant_id');
    }
}
