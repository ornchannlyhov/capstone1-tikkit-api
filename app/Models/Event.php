<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'startDate', 'endDate', 'category_id', 'status'];

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

    // Auto-update status based on the start and end dates
    public function updateStatus()
    {
        $now = Carbon::now();
        if ($now->lt($this->startDate)) {
            $this->status = 'upcoming';
        } elseif ($now->between($this->startDate, $this->endDate)) {
            $this->status = 'active';
        } else {
            $this->status = 'passed';
        }

        $this->save();
    }
}

