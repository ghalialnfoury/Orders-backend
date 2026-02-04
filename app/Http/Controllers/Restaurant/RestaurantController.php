<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    // إنشاء مطعم (مرة واحدة فقط)
    public function store(Request $request)
    {
        $userId = auth('api')->id();

        // منع إنشاء أكثر من مطعم لنفس المستخدم
        if (Restaurant::where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'Restaurant already exists'
            ], 409);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        $restaurant = Restaurant::create(array_merge(
            $validated,
            ['user_id' => $userId]
        ));

        return response()->json($restaurant, 201);
    }

    // عرض بيانات مطعم المستخدم
    public function show()
    {
        $restaurant = Restaurant::where('user_id', auth('api')->id())
            ->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant not found'
            ], 404);
        }

        return response()->json($restaurant);
    }
}
