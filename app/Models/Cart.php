<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'ticket_id', 'quantity', 'event_id', 'status'];

    protected $attributes = [
        'status' => self::STATUS_PENDING, // Ensure default is pending
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCEL_REQUEST = 'cancel_request';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCEL_REQUEST,
            self::STATUS_CANCELLED,
        ];
    }

    public function setStatusAttribute($value)
    {
        $validStatuses = self::getStatusOptions();
        if (!in_array($value, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status value: $value");
        }
        $this->attributes['status'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsToMany(Order::class, 'order_cart');
    }

    public function ticketOption()
    {
        return $this->belongsTo(TicketOption::class, 'ticket_id');
    }
}