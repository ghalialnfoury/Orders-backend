<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'restaurant_id',
        'driver_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /* ================= RELATIONS ================= */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /* ================= SCOPES ================= */

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
