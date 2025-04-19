<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function rateSeller(Request $request, $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $order = Orders::findOrFail($orderId);

        // تأكد من أن المستخدم الحالي هو المشتري
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'غير مسموح لك بتقييم هذا البائع.'], 403);
        }

        // التحقق من وجود البائع في الطلب
        if (is_null($order->seller_id)) {
            return response()->json(['message' => 'لا يمكن تقييم هذا الطلب لأنه لا يحتوي على بائع.'], 400);
        }

        // التحقق من أن الطلب لم يتم تقييمه من قبل
        $existingRating = Rating::where('order_id', $orderId)->first();
        if ($existingRating) {
            return response()->json(['message' => 'تم تقييم هذا الطلب بالفعل.'], 400);
        }

        // إنشاء التقييم
        try {
            $rating = Rating::create([
                'order_id' => $orderId,
                'buyer_id' => Auth::id(),
                'seller_id' => $order->seller_id,
                'rating' => $request->rating,
                'review' => $request->review,
            ]);

            return response()->json(['message' => 'تم تقديم التقييم بنجاح.', 'rating' => $rating], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء إنشاء التقييم: ' . $e->getMessage()], 500);
        }
    }
}
