<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'startDate', 'endDate', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function ticketOptions()
    {
        return $this->hasMany(TicketOption::class);
    }
}

