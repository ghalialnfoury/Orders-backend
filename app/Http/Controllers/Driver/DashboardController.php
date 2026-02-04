<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;

class DashboardController extends Controller
{
    public function index()
    {
        $driverId = auth('api')->id();

        return response()->json([
            'total_orders'   => Order::where('driver_id', $driverId)->count(),
            'on_the_way'     => Order::where('driver_id', $driverId)
                                    ->where('status', 'on_the_way')
                                    ->count(),
            'delivered'      => Order::where('driver_id', $driverId)
                                    ->where('status', 'delivered')
                                    ->count(),
            'average_rating' => round(
                Rating::where('driver_id', $driverId)->avg('rating'),
                1
            ),
        ]);
    }
}
