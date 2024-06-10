<?php

namespace App\Http\Controllers;

use App\Models\Bids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class BidsController extends Controller
{
    // دالة لإضافة المزايدة
    public function store(Request $request)
    {
        // تعريف قواعد التحقق
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'bid_amount' => 'required|numeric|min:0',
        ], [
            'user_id.required' => 'معرف المستخدم مطلوب.',
            'user_id.exists' => 'معرف المستخدم غير موجود.',
            'product_id.required' => 'معرف المنتج مطلوب.',
            'product_id.exists' => 'معرف المنتج غير موجود.',
            'bid_amount.required' => 'سعر المزايدة مطلوب.',
            'bid_amount.numeric' => 'سعر المزايدة يجب أن يكون رقمًا.',
            'bid_amount.min' => 'سعر المزايدة يجب أن يكون أكبر من أو يساوي 0.',
        ]);

        // التحقق من الأخطاء
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            // إنشاء سجل المزايدة
            $bid = Bids::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'bid_amount' => $request->bid_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المزايدة بنجاح',
                'bid' => $bid,
            ], 201);
        } catch (QueryException $e) {
            // إظهار رسالة خطأ مخصصة إذا كان هناك مشكلة في المفتاح الأجنبي
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'تعذر إضافة المزايدة بسبب مشكلة في المفتاح الأجنبي. تأكد من صحة معرف المستخدم ومعرف المنتج.',
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المزايدة. تأكد من صحة البيانات المدخلة.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // دالة لجلب جميع المزايدات
    public function index()
    {
        // جلب جميع المزايدات مع بيانات المستخدم والمنتج المرتبطة
        $bids = Bids::with(['user', 'product'])->get();

        // تخصيص البيانات المسترجعة
        $bids = $bids->map(function ($bid) {
            return [
                'id' => $bid->id,
                'user_id' => $bid->user_id,
                'product_id' => $bid->product_id,
                'bid_amount' => $bid->bid_amount,
                'created_at' => $bid->created_at,
                'updated_at' => $bid->updated_at,
                'product' => [
                    'name' => $bid->product->product_name,
                    'description' => $bid->product->product_description,
                    'price' => $bid->product->product_price,
                    'category' => $bid->product->product_category,
                    'stock' => $bid->product->product_stock,
                    'status' => $bid->product->product_status,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'bids' => $bids,
        ], 200);
    }

    // دالة لجلب المزايدات الخاصة بمستخدم معين
    public function getUserBids($user_id)
    {
        // جلب المزايدات التي قام بها المستخدم فقط
        $bids = Bids::with(['user', 'product'])->where('user_id', $user_id)->get();

        // تخصيص البيانات المسترجعة
        $bids = $bids->map(function ($bid) {
            return [
                'id' => $bid->id,
                'user_id' => $bid->user_id,
                'product_id' => $bid->product_id,
                'bid_amount' => $bid->bid_amount,
                'created_at' => $bid->created_at,
                'updated_at' => $bid->updated_at,
                'product' => [
                    'name' => $bid->product->product_name,
                    'description' => $bid->product->product_description,
                    'price' => $bid->product->product_price,
                    'category' => $bid->product->product_category,
                    'stock' => $bid->product->product_stock,
                    'status' => $bid->product->product_status,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'bids' => $bids,
        ], 200);
    }
}
