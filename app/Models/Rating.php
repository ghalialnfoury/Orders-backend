<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rating Model
 *
 * Represents customer ratings submitted after order completion.
 * Stores rating details related to:
 * - Order
 * - Customer
 * - Restaurant
 * - Driver
 *
 * Includes relationships and reusable query scopes.
 */
class Rating extends Model
{
    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'order_id',
        'customer_id',
        'restaurant_id',
        'driver_id',
        'rating',
        'comment'
    ];

    /**
     * Attribute casting
     * Ensures rating is always treated as an integer
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /* =====================================================
     * RELATIONSHIPS
     * ===================================================== */

    /**
     * Get the order associated with this rating
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer who submitted the rating
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the restaurant that was rated
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the driver that was rated
     * May be null if no driver was assigned
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /* =====================================================
     * SCOPES
     * ===================================================== */

    /**
     * Scope to filter ratings by restaurant
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $restaurantId
     */
    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Scope to filter ratings by driver
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $driverId
     */
    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
