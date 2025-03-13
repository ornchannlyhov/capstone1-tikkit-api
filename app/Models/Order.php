<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'order_cart');
    }

    public function transaction()
    {
        return $this->hasOne(PaymentTransaction::class);
    }
}
