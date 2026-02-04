<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;

class AdminRestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('owner:id,name,email')
            ->latest()
            ->paginate(15);

        return response()->json($restaurants);
    }

    public function approve($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if ($restaurant->is_open) {
            return response()->json([
                'message' => 'Restaurant is already approved'
            ], 409);
        }

        $restaurant->update([
            'is_open' => true,
        ]);

        return response()->json([
            'message'    => 'Restaurant approved successfully',
            'restaurant' => $restaurant
        ]);
    }
}
