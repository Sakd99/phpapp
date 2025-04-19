<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    // عرض كل العناوين للمستخدم الحالي
    public function index()
    {
        $addresses = Auth::user()->addresses;

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    // إضافة عنوان جديد
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'nearest_landmark' => 'nullable|string|max:255',
        ]);

        $address = Address::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'governorate' => $request->governorate,
            'district' => $request->district,
            'area' => $request->area,
            'nearest_landmark' => $request->nearest_landmark,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ], 201);
    }

    // تحديث عنوان
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'governorate' => 'sometimes|required|string|max:255',
            'district' => 'sometimes|required|string|max:255',
            'area' => 'sometimes|required|string|max:255',
            'nearest_landmark' => 'nullable|string|max:255',
        ]);

        $address->update($request->only(['title', 'governorate', 'district', 'area', 'nearest_landmark']));

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }

    // حذف عنوان
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully',
        ]);
    }
}
