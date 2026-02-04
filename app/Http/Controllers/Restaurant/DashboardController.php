<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();

        if (!$user || !$user->restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        $restaurantId = $user->restaurant->id;

        return response()->json([
            'total_orders'      => Order::where('restaurant_id', $restaurantId)->count(),

            'pending_orders'    => Order::where('restaurant_id', $restaurantId)
                ->whereIn('status', ['pending', 'accepted', 'preparing'])
                ->count(),

            'completed_orders'  => Order::where('restaurant_id', $restaurantId)
                ->where('status', 'delivered')
                ->count(),

            'total_revenue'     => Order::where('restaurant_id', $restaurantId)
                ->where('payment_status', 'paid')
                ->sum('total_price'),

            'average_rating'    => round(
                Rating::where('restaurant_id', $restaurantId)->avg('rating') ?? 0,
                1
            ),
        ]);
    }
}
