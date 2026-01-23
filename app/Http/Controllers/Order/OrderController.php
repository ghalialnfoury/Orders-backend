<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;

        $order = Order::create([
            'user_id' => auth('api')->id(),
            'restaurant_id' => $request->restaurant_id,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price * $item['quantity'];
            $total += $price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $price,
            ]);
        }

        $order->update(['total_price' => $total]);

        return response()->json($order->load('items'), 201);
    }

    public function myOrders()
    {
        return Order::where('user_id', auth('api')->id())
            ->with('items.product')
            ->get();
    }
    // اختيار طريقة الدفع
public function setPaymentMethod(Request $request, $id)
{
    $validated = $request->validate([
        'payment_method' => 'required|in:cash,card'
    ]);

    $order = Order::where('user_id', auth('api')->id())
        ->where('id', $id)
        ->firstOrFail();

    $order->update([
        'payment_method' => $validated['payment_method']
    ]);

    return response()->json($order);
}

        public function pay($id)
    {
        $order = Order::where('user_id', auth('api')->id())
            ->where('id', $id)
            ->firstOrFail();

        $order->update([
            'payment_status' => 'paid'
        ]);

        return response()->json([
            'message' => 'Payment successful',
            'order' => $order
        ]);
    }

}
