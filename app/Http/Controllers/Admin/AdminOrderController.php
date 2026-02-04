<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | 📦 List Orders
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $orders = Order::with([
            'user:id,name,email',
            'restaurant:id,name',
            'driver:id,name,email',
            'items.product:id,name,price'
        ])
        ->latest()
        ->paginate(20);

        return response()->json($orders);
    }


    /*
    |--------------------------------------------------------------------------
    | 📄 Show Order
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'restaurant',
            'driver',
            'items.product'
        ])->findOrFail($id);

        return response()->json($order);
    }


    /*
    |--------------------------------------------------------------------------
    | 🔄 Change Order Status
    |--------------------------------------------------------------------------
    */
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,preparing,on_the_way,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);

        if ($order->status === $validated['status']) {
            return response()->json([
                'message' => 'Status already set'
            ], 409);
        }

        $order->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'message' => 'Order status updated',
            'order'   => $order
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ❌ Cancel Order
    |--------------------------------------------------------------------------
    */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'delivered') {
            return response()->json([
                'message' => 'Cannot cancel delivered order'
            ], 403);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 🚚 Assign Driver
    |--------------------------------------------------------------------------
    */
    public function assignDriver(Request $request, $id)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        $driver = User::findOrFail($validated['driver_id']);

        if ($driver->role !== 'driver') {
            return response()->json([
                'message' => 'User is not a driver'
            ], 403);
        }

        $order = Order::findOrFail($id);

        $order->update([
            'driver_id' => $driver->id,
            'status'    => 'on_the_way'
        ]);

        return response()->json([
            'message' => 'Driver assigned successfully',
            'order'   => $order
        ]);
    }
}
