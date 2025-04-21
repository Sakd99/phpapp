<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the user's payment methods.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods()->get();

        return response()->json([
            'message' => 'تم جلب طرق الدفع بنجاح',
            'payment_methods' => $paymentMethods
        ], 200);
    }

    /**
     * Store a newly created payment method in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // التحقق من عدم وجود نفس طريقة الدفع لنفس المستخدم
        $exists = $user->paymentMethods()
            ->where('type', $request->type)
            ->where('account_number', $request->account_number)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'طريقة الدفع موجودة بالفعل'
            ], 422);
        }

        // إذا كانت طريقة الدفع الجديدة هي الافتراضية، قم بتعيين جميع الطرق الأخرى إلى غير افتراضية
        if ($request->is_default) {
            $user->paymentMethods()->update(['is_default' => false]);
        }

        $paymentMethod = new PaymentMethod([
            'type' => $request->type,
            'account_number' => $request->account_number,
            'is_default' => $request->is_default ?? false,
            'is_active' => true
        ]);

        $user->paymentMethods()->save($paymentMethod);

        return response()->json([
            'message' => 'تم إضافة طريقة الدفع بنجاح',
            'payment_method' => $paymentMethod
        ], 201);
    }

    /**
     * Display the specified payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()->find($id);

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'طريقة الدفع غير موجودة'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب طريقة الدفع بنجاح',
            'payment_method' => $paymentMethod
        ], 200);
    }

    /**
     * Update the specified payment method in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|string|max:255',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()->find($id);

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'طريقة الدفع غير موجودة'
            ], 404);
        }

        // التحقق من عدم وجود نفس طريقة الدفع لنفس المستخدم
        if ($request->has('type') && $request->has('account_number')) {
            $exists = $user->paymentMethods()
                ->where('id', '!=', $id)
                ->where('type', $request->type)
                ->where('account_number', $request->account_number)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'طريقة الدفع موجودة بالفعل'
                ], 422);
            }
        }

        // إذا كانت طريقة الدفع المحدثة هي الافتراضية، قم بتعيين جميع الطرق الأخرى إلى غير افتراضية
        if ($request->has('is_default') && $request->is_default) {
            $user->paymentMethods()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $paymentMethod->update($request->only(['type', 'account_number', 'is_default', 'is_active']));

        return response()->json([
            'message' => 'تم تحديث طريقة الدفع بنجاح',
            'payment_method' => $paymentMethod
        ], 200);
    }

    /**
     * Remove the specified payment method from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()->find($id);

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'طريقة الدفع غير موجودة'
            ], 404);
        }

        // إذا كانت طريقة الدفع المحذوفة هي الافتراضية، قم بتعيين طريقة أخرى كافتراضية إذا وجدت
        if ($paymentMethod->is_default) {
            $anotherPaymentMethod = $user->paymentMethods()->where('id', '!=', $id)->first();
            if ($anotherPaymentMethod) {
                $anotherPaymentMethod->update(['is_default' => true]);
            }
        }

        $paymentMethod->delete();

        return response()->json([
            'message' => 'تم حذف طريقة الدفع بنجاح'
        ], 200);
    }
}
