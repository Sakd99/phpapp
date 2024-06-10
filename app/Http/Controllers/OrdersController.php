<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email|max:255',
            'buyer_phone' => 'required|string|max:255',
            'buyer_address' => 'required|string|max:255',
            'buyer_city' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Orders::create([
            'user_id' => $request->user_id,
            'order_number' => uniqid('ORDER-'),
            'order_status' => 'Pending',
            'buyer_name' => $request->buyer_name,
            'buyer_email' => $request->buyer_email,
            'buyer_phone' => $request->buyer_phone,
            'buyer_address' => $request->buyer_address,
            'buyer_city' => $request->buyer_city,
            'total' => 0, // سيتم حساب الإجمالي لاحقاً
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $product = Products::find($item['product_id']);
            $quantity = $item['quantity'];
            $price = floatval($product->product_price) * $quantity; // تحويل product_price إلى رقم

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $product->product_name, // تأكد من جلب اسم المنتج
                'quantity' => $quantity,
                'price' => $price,
            ]);

            $total += $price;
        }

        $order->update(['total' => $total]);

        return response()->json([
            'success' => true,
            'order' => $order->load('items.product'),
        ]);
    }
}
