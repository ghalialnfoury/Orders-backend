<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // كل طلبات المطعم
    public function index()
    {
        $restaurantId = auth('api')->user()->restaurant->id;

        return response()->json(
            Order::where('restaurant_id', $restaurantId)
                ->with([
                    'user:id,name,phone',
                    'driver:id,name,phone',
                    'items.product:id,name,price',
                ])
                ->latest()
                ->get()
        );
    }

    // تحديث حالة الطلب (بدون درايفر)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,preparing',
        ]);

        $restaurantId = auth('api')->user()->restaurant->id;

        $order = Order::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->firstOrFail();

        $allowed = [
            'pending'  => ['accepted'],
            'accepted' => ['preparing'],
        ];

        if (
            !isset($allowed[$order->status]) ||
            !in_array($request->status, $allowed[$order->status])
        ) {
            return response()->json([
                'message' => 'Invalid order status transition',
            ], 400);
        }

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json($order->fresh());
    }

    // إسناد درايفر (خطوة منفصلة)
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $restaurantId = auth('api')->user()->restaurant->id;

        $driver = User::where('id', $request->driver_id)
            ->where('role', 'driver')
            ->where('status', 'active')
            ->first();

        if (!$driver) {
            return response()->json([
                'message' => 'Invalid or inactive driver',
            ], 422);
        }

        $order = Order::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->firstOrFail();

        if ($order->status !== 'preparing') {
            return response()->json([
                'message' => 'Order must be preparing before assigning driver',
            ], 400);
        }

        if ($order->driver_id) {
            return response()->json([
                'message' => 'Driver already assigned',
            ], 409);
        }

        $order->update([
            'driver_id' => $driver->id,
            'status'    => 'on_the_way',
        ]);

        return response()->json(
            $order->load('driver:id,name,phone')
        );
    }
}
