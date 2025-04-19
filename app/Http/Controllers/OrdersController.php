<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\Bids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use App\Notifications\OrderStatusNotification;


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
            'bid_id' => 'required|exists:bids,id',
        ]);

        $bid = Bids::find($request->bid_id);

        if ($bid->end_time > now() || $bid->status !== 'sold' || $bid->buyer_id != $request->user_id) {
            return response()->json(['message' => 'This user did not win the bid or the bid is still ongoing'], 400);
        }

        DB::beginTransaction();
        try {
            $orderData = [
                'user_id' => $request->user_id,
                'order_number' => uniqid('ORDER-'),
                'order_status' => 'Pending',
                'buyer_name' => $request->buyer_name,
                'buyer_email' => $request->buyer_email,
                'buyer_phone' => $request->buyer_phone,
                'buyer_address' => $request->buyer_address,
                'buyer_city' => $request->buyer_city,
                'total' => $bid->current_price,
            ];

            $order = Orders::create($orderData);

            // تحديث قيم `bid_id` و `seller_id`
            $order->update([
                'bid_id' => $bid->id,
                'seller_id' => $bid->user_id,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $bid->id,
                'product_name' => $bid->product_name,
                'quantity' => 1,
                'price' => $bid->current_price,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->load('items.product'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create the order'], 500);
        }
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        // تسجيل الدخول إلى الدالة
        Log::info('Entering updateOrderStatus function', ['order_id' => $orderId]);

        // تحديث حالة الطلب
        $order = Orders::findOrFail($orderId);
        $order->order_status = $request->input('status');
        $order->save();

        // إعداد الرسالة
        $message = "تم تحديث حالة طلبك إلى: {$order->order_status}";

        try {
            // جلب phone_id من المزايدة المرتبطة بالطلب
            $bid = $order->bid;
            $bidder = $bid ? $bid->bidders()->where('user_id', $order->user_id)->first() : null;

            if (!$bidder) {
                Log::warning('Failed to retrieve bidder for order: ' . $order->id);
            }

            $phoneId = $bidder ? $bidder->pivot->phone_id : null;

            if ($phoneId) {
                Log::info('Attempting to send notification.', [
                    'order_id' => $order->id,
                    'phone_id' => $phoneId,
                    'status' => $order->order_status,
                ]);

                // إرسال الإشعار
                try {
                    $order->notify(new OrderStatusNotification($order, $message));
                    Log::info('Notification sent successfully.');
                } catch (\Exception $e) {
                    Log::error('Failed to send notification via notify(): ' . $e->getMessage());
                }
            } else {
                Log::warning('Phone ID is missing. Notification not sent.', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['message' => 'تم تحديث حالة الطلب وإرسال الإشعار بنجاح.']);
    }








    public function updateOrderIds(Request $request, $orderId)
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'bid_id' => 'required|exists:bids,id',
                'seller_id' => 'required|exists:users,id',
            ]);

            // العثور على الطلب
            $order = Orders::findOrFail($orderId);

            // تحديث قيم bid_id و seller_id
            $order->bid_id = $request->bid_id;
            $order->seller_id = $request->seller_id;

            // حفظ التغييرات
            if ($order->save()) {
                return response()->json([
                    'message' => 'تم تحديث الطلب بنجاح',
                    'order' => $order,
                ], 200);
            } else {
                return response()->json(['message' => 'فشل في تحديث الطلب'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage()], 500);
        }
    }

    public function userOrders($userId)
    {
        $orders = Orders::where('user_id', $userId)
            ->with(['items.product', 'bid.bidders', 'bid.user', 'seller']) // تضمين العلاقتين bid و bidders
            ->get();

        $orders = $orders->map(function ($order) {
            // جلب عنوان الشحن من الـ pivot الخاص بالمزايدين
            $shippingAddress = optional($order->bid->bidders->where('id', $order->user_id)->first())->pivot->shipping_address ?? 'غير متوفر';

            return [
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'buyer_name' => $order->buyer_name,
                'buyer_email' => $order->buyer_email,
                'buyer_phone' => $order->buyer_phone,
                'buyer_address' => $shippingAddress, // استخدام عنوان الشحن من الـ pivot
                'buyer_city' => $order->buyer_city,
                'total' => $order->total,
                'product_name' => $order->bid ? $order->bid->product_name : null,
                'initial_price' => $order->bid ? $order->bid->initial_price : null,
                'current_price' => $order->bid ? $order->bid->current_price : null,
                'end_time' => $order->bid ? $order->bid->end_time : null,
                'seller_info' => $order->seller ? [
                    'name' => $order->seller->name,
                    'email' => $order->seller->email,
                    'phone' => $order->seller->phone,
                    'region' => $order->bid->region ?? 'غير متوفر',
                    'governorate' => $order->bid->governorate ?? 'غير متوفر',
                ] : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ];
        });

        return response()->json(['orders' => $orders]);
    }

    public function exportOrderPdf($id)
    {
        $order = Orders::with(['items', 'bid', 'seller'])->findOrFail($id);

        // إنشاء محتوى HTML لملف PDF
        $html = view('orders.single_pdf', compact('order'))->render();

        // إعداد mpdf
        $mpdf = new Mpdf([
            'default_font' => 'dejavusans', // تأكد من استخدام خط يدعم العربية والإنجليزية
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P'
        ]);

        // كتابة المحتوى في ملف PDF
        $mpdf->WriteHTML($html);

        // تنزيل ملف PDF
        return $mpdf->Output('order_' . $order->order_number . '.pdf', 'D');
    }

}
