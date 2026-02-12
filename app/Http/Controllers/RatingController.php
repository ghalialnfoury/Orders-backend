<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;

/**
 * RatingController
 *
 * Handles customer rating submissions for completed orders.
 * Ensures:
 * - Order belongs to authenticated user
 * - Order is delivered before rating
 * - Prevents duplicate ratings
 */
class RatingController extends Controller
{
    /**
     * Store a new rating for a specific order
     *
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $orderId)
    {
        // Validate incoming rating data
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Get authenticated user ID
        $userId = auth('api')->id();

        /**
         * Ensure:
         * - Order exists
         * - Order belongs to authenticated user
         * - Order has been delivered
         */
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->first();

        // Return error if order not found or not delivered
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not delivered yet'
            ], 404);
        }

        /**
         * Prevent duplicate rating for the same order
         */
        if (Rating::where('order_id', $orderId)->exists()) {
            return response()->json([
                'message' => 'Order already rated'
            ], 409);
        }

        /**
         * Create rating record
         * Stores references to:
         * - Customer
         * - Restaurant
         * - Driver
         */
        $rating = Rating::create([
            'order_id'      => $order->id,
            'customer_id'   => $userId,
            'restaurant_id' => $order->restaurant_id,
            'driver_id'     => $order->driver_id ?? null,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'] ?? null,
        ]);

        // Return success response
        return response()->json([
            'message' => 'Rating submitted successfully',
            'rating'  => $rating
        ], 201);
    }
}
