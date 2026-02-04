<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(int $restaurantId)
    {
        return response()->json(
            Product::where('restaurant_id', $restaurantId)
                ->orderBy('name')
                ->get()
        );
    }
}
