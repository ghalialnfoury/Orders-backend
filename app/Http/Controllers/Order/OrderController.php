<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id'        => 'required|exists:restaurants,id',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {

            $order = Order::create([
                'user_id'        => auth('api')->id(),
                'restaurant_id'  => $request->restaurant_id,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'total_price'    => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::select('id', 'price')
                    ->findOrFail($item['product_id']);

                $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id'=> $product->id,
                    'quantity'  => $item['quantity'],
                    'price'     => $itemTotal,
                ]);
            }

            $order->update([
                'total_price' => $total
            ]);

            return response()->json(
                $order->load([
                    'restaurant:id,name',
                    'items.product:id,name,price',
                ]),
                201
            );
        });
    }

    /**
     * طلبات المستخدم
     */
    public function myOrders()
    {
        return response()->json(
            Order::where('user_id', auth('api')->id())
                ->with([
                    'restaurant:id,name',
                    'items.product:id,name,price',
                ])
                ->orderByDesc('created_at')
                ->get()
        );
    }

    /**
     * تحديد طريقة الدفع
     */
    public function setPaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'Order already paid'
            ], 400);
        }

        $order->update([
            'payment_method' => $request->payment_method,
        ]);

        return response()->json($order);
    }

    /**
     * تأكيد الدفع
     */
    public function pay($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'message' => 'Order already paid'
            ], 400);
        }

        $order->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);

        return response()->json([
            'message' => 'Payment successful',
            'order'   => $order
        ]);
    }
}
