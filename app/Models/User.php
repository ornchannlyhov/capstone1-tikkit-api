<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'provider',
        'provider_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isBanned()
    {
        return !is_null($this->banned_at);
    }

    public function ban()
    {
        $this->banned_at = now();
        $this->save();
    }

    public function unban()
    {
        $this->banned_at = null;
        $this->save();
    }

}
