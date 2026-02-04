<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * الطلبات المسندة للدرايفر
     */
    public function index()
    {
        $driverId = auth('api')->id();

        $orders = Order::where('driver_id', $driverId)
            ->with([
                'user:id,name,phone',
                'restaurant:id,name',
                'items.product:id,name,price',
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    /**
     * تحديث حالة الطلب (on_the_way / delivered)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:on_the_way,delivered'
        ]);

        $order = Order::where('id', $id)
            ->where('driver_id', auth('api')->id())
            ->firstOrFail();

        // منع التحديث إذا كان الطلب مسلّم
        if ($order->status === 'delivered') {
            return response()->json([
                'message' => 'Order already delivered'
            ], 400);
        }

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order'   => $order
        ]);
    }

    /**
     * تأكيد التسليم
     */
    public function deliver($id)
    {
        $order = Order::where('id', $id)
            ->where('driver_id', auth('api')->id())
            ->firstOrFail();

        if ($order->status !== 'on_the_way') {
            return response()->json([
                'message' => 'Order is not on the way'
            ], 400);
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return response()->json([
            'message' => 'Order delivered successfully',
            'order'   => $order
        ]);
    }
}
