<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'address',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /* ================= ACCESSORS ================= */

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1);
    }

    /* ================= SCOPES ================= */

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }
}
