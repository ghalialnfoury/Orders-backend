<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = auth('api')->id();

        // الطلب يجب أن يكون للمستخدم ومسلّم
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not delivered yet'
            ], 404);
        }

        // منع التقييم المكرر
        if (Rating::where('order_id', $orderId)->exists()) {
            return response()->json([
                'message' => 'Order already rated'
            ], 409);
        }

        $rating = Rating::create([
            'order_id'      => $order->id,
            'customer_id'   => $userId,
            'restaurant_id' => $order->restaurant_id,
            'driver_id'     => $order->driver_id,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'Rating submitted successfully',
            'rating'  => $rating
        ], 201);
    }
}
