<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $restaurant = Restaurant::create([
            'user_id' => auth('api')->id(),
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return response()->json($restaurant, 201);
    }

    public function show()
    {
        $restaurant = Restaurant::where('user_id', auth('api')->id())->first();

        return response()->json($restaurant);
    }
}
