<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'street', 'city', 'country', 'venue_name', 'extra_info'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
