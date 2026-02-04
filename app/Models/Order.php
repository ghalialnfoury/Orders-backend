<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'driver_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
    ];

    /* ================= RELATIONS ================= */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /* ================= SCOPES (OPTIONAL) ================= */

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
