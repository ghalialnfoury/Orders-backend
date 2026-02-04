<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'users_count'            => User::count(),
            'restaurants_count'      => Restaurant::count(),
            'orders_count'           => Order::count(),

            'pending_orders_count'   => Order::where('status', 'pending')->count(),
            'completed_orders_count' => Order::where('status', 'delivered')->count(),

            'total_revenue'          => Order::where('payment_status', 'paid')
                                              ->sum('total_price'),

            'average_order_value'    => Order::where('payment_status', 'paid')
                                              ->avg('total_price'),
        ]);
    }
}
