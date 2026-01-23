<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('driver_id', auth('api')->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:on_the_way,delivered'
        ]);

        $order = Order::where('driver_id', auth('api')->id())
            ->where('id', $id)
            ->firstOrFail();

        $order->update([
            'status' => $validated['status']
        ]);

        return response()->json($order);
    }
}
