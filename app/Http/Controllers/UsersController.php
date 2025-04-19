<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    // جلب جميع المستخدمين
    public function index()
    {
        return Users::all();
    }

    // إضافة مستخدم جديد
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:15',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_verified' => 'nullable|boolean' // التحقق من الحقل الجديد
        ]);

        // إذا كان هناك صورة مرفوعة، قم بحفظها في المسار المحدد
        if ($request->hasFile('profile_photo')) {
            $validatedData['profile_photo_path'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        // تشفير كلمة المرور قبل التخزين
        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = Users::create($validatedData);

        return response()->json(['message' => 'تم إنشاء المستخدم بنجاح', 'user' => $user], 201);
    }

    // جلب بيانات مستخدم محدد
    public function show(Users $users)
    {
        return $users;
    }

    // تحديث بيانات المستخدم
    public function update(Request $request, Users $users)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_verified' => 'nullable|boolean' // التحقق من الحقل الجديد
        ]);

        // إذا كان هناك صورة مرفوعة، قم بحفظها
        if ($request->hasFile('profile_photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($users->profile_photo_path) {
                Storage::delete('public/' . $users->profile_photo_path);
            }

            // حفظ الصورة الجديدة في المسار المحدد
            $validatedData['profile_photo_path'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        // تحديث البيانات
        $users->update($validatedData);

        return response()->json(['message' => 'تم تحديث بيانات المستخدم بنجاح', 'user' => $users], 200);
    }

    // تحديث بيانات المستخدم المسجل (المصادق عليه)
    public function updateProfile(Request $request)
    {
        // جلب المستخدم المصادق عليه (الذي قام بتسجيل الدخول)
        $user = auth()->user();

        // التحقق من البيانات المدخلة
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // إذا كان هناك صورة مرفوعة، قم بحفظها
        if ($request->hasFile('profile_photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }

            // حفظ الصورة الجديدة في المسار المحدد
            $validatedData['profile_photo_path'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        // تحديث بيانات المستخدم
        $user->update($validatedData);

        // إعادة الاستجابة مع رابط الصورة المحدث
        return response()->json([
            'message' => 'تم تحديث بيانات المستخدم بنجاح',
            'user' => $user,
            'profile_photo_url' => asset('storage/' . $user->profile_photo_path)
        ], 200);
    }

    // حذف مستخدم
    public function destroy(Users $users)
    {
        // حذف الصورة المرتبطة بالمستخدم إذا كانت موجودة
        if ($users->profile_photo_path) {
            Storage::delete('public/' . $users->profile_photo_path);
        }

        $users->delete();

        return response()->json(['message' => 'تم حذف المستخدم بنجاح'], 200);
    }
}
