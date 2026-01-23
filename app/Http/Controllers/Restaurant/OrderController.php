<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $restaurantId = auth('api')->user()
            ->restaurant
            ->id;

        $orders = Order::where('restaurant_id', $restaurantId)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:accepted,preparing,on_the_way'
    ]);

    $restaurant = auth('api')->user()->restaurant;

    $order = Order::where('restaurant_id', $restaurant->id)
        ->where('id', $id)
        ->firstOrFail();

    $order->update([
        'status' => $request->status
    ]);

    return response()->json($order);
}

    // public function updateStatus($id)
    // {
    //     $order = Order::findOrFail($id);

    //     $order->update([
    //         'status' => request('status')
    //     ]);

    //     return response()->json($order);
    // }
  public function assignDriver(Request $request, $id)
{
    $validated = $request->validate([
        'driver_id' => [
            'required',
            // هذا يتحقق من وجود user بالـ id ودوره driver فقط
            function ($attribute, $value, $fail) {
                $driver = \App\Models\User::where('id', $value)
                                           ->where('role', 'driver')
                                           ->first();
                if (!$driver) {
                    $fail('The selected driver id is invalid.');
                }
            }
        ],
    ]);

    $restaurant = auth('api')->user()->restaurant;

    $order = Order::where('restaurant_id', $restaurant->id)
                  ->where('id', $id)
                  ->firstOrFail();

    $order->update([
        'driver_id' => $validated['driver_id'],
        'status' => 'on_the_way'
    ]);

    return response()->json($order);
}


}
