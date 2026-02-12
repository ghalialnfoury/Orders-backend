<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    protected $attributes = [
        'is_available' => true,
    ];

    protected $appends = ['image_url'];

    /* RELATIONSHIPS */

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /* SCOPES */

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
/* =====================================================
 * ACCESSORS
 * ===================================================== */

public function getImageUrlAttribute()
{
    return $this->image
        ? asset('storage/' . $this->image)
        : null;
}


}
