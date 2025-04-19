<?php

namespace App\Http\Controllers;

use App\Models\Bids;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Notifications\BidNotification;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;




class BidsController extends Controller
{


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string',
            'initial_price' => 'required|numeric',
            'current_price' => 'required|numeric',
            'end_time' => 'required|date',
            'product_image1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_image4' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_image5' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'parent_id' => 'nullable|exists:sub_categories,id',
            'governorate' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'shipping_option' => 'required|in:seller,buyer',
            'product_condition' => 'required|in:new,used',
            'fcm_token' => 'required|string|max:255', // جعل الحقل مطلوبًا
            'available_colors' => 'nullable|array',
            'available_sizes' => 'nullable|array',
        ]);

        try {
            $bid = new Bids();
            $bid->fill($validatedData);
            $bid->status = 'pending';
            $bid->user_id = Auth::user()->id;
            $bid->fcm_token = $validatedData['fcm_token']; // حفظ fcm_token في قاعدة البيانات

            // حفظ الصور
            if ($request->hasFile('product_image1')) {
                $bid->product_image1 = $request->file('product_image1')->store('bids', 'public');
            }
            if ($request->hasFile('product_image2')) {
                $bid->product_image2 = $request->file('product_image2')->store('bids', 'public');
            }
            if ($request->hasFile('product_image3')) {
                $bid->product_image3 = $request->file('product_image3')->store('bids', 'public');
            }
            if ($request->hasFile('product_image4')) {
                $bid->product_image4 = $request->file('product_image4')->store('bids', 'public');
            }
            if ($request->hasFile('product_image5')) {
                $bid->product_image5 = $request->file('product_image5')->store('bids', 'public');
            }

            // حفظ البيانات الأخرى
            $bid->delivery_address = $validatedData['delivery_address'];
            $bid->governorate = $validatedData['governorate'];
            $bid->region = $validatedData['region'];
            $bid->subcategory_id = $validatedData['subcategory_id'];
            $bid->parent_id = $validatedData['parent_id']; // حفظ parent_id
            $bid->shipping_option = $validatedData['shipping_option'];
            $bid->product_condition = $validatedData['product_condition'];

            if ($bid->save()) {
                // جلب معلومات المستخدم الذي قام بنشر المزايدة
                $user = Auth::user();

                return response()->json([
                    'message' => 'تم إنشاء المزاد بنجاح',
                    'bid' => $bid,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ]
                ], 201);
            } else {
                return response()->json(['message' => 'فشل في إنشاء المزاد'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء إنشاء المزاد: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $sort = $request->query('sort', 'desc');

        $bids = Bids::with(['user', 'bidders' => function ($query) {
            $query->orderBy('bid_user.bid_amount', 'desc');
        }])
            ->orderBy('current_price', $sort)
            ->get();

        foreach ($bids as $bid) {
            // تحديث مسارات الصور
            if (!empty($bid->product_image1)) {
                $bid->product_image1 = asset('storage/app/public/' . $bid->product_image1);
            }

            // تضمين معلومات المستخدم الذي قام بإنشاء المزايدة
            $bid->user_info = $bid->user ? [
                'id' => $bid->user->id,
                'name' => $bid->user->name,
                'email' => $bid->user->email,
                'phone' => $bid->user->phone,
                'is_verified' => $bid->user->is_verified,
            ] : null;

            // تضمين خيار التوصيل في الاستجابة
            $bid->shipping_option_text = $bid->shipping_option === 'seller' ? 'التوصيل على البائع' : 'التوصيل على المشتري';

            // تضمين حالة المنتج في الاستجابة
            $bid->product_condition_text = $bid->product_condition === 'new' ? 'جديد' : 'مستخدم';

            // تضمين الألوان والأحجام المتاحة
            $bid->available_colors_text = is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null;
            $bid->available_sizes_text = is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null;

            // لا حاجة لإعادة تعيين parent_id، لأنه موجود بالفعل في النموذج
            // وستتم إعادته تلقائيًا في استجابة JSON
        }

        return response()->json([
            'data' => $bids,
            'sort' => $sort,
        ]);
    }

    public function checkAuctionExpiry()
    {
        // جلب جميع المزايدات التي انتهى وقتها أو وصلت إلى السعر المطلوب ولم يتم تحويلها إلى طلب بعد
        $bids = Bids::where('status', '!=', 'sold')
            ->where(function ($query) {
                $query->where('end_time', '<=', now())
                    ->orWhereColumn('current_price', 'initial_price'); // التحقق من الوصول للسعر المطلوب
            })
            ->get();

        foreach ($bids as $bid) {
            // الحصول على المزايد الأعلى
            $highestBidder = $bid->bidders()->orderBy('bid_amount', 'desc')->first();

            if ($highestBidder) {
                // استدعاء دالة إنشاء الطلب
                $order = $this->createOrderFromBid($bid, $highestBidder);

                if ($order) {
                    $bid->status = 'sold'; // تحديث حالة المزايدة
                    $bid->save();

                    Log::info('Order created successfully from bid:', ['bidId' => $bid->id, 'orderId' => $order->id]);
                }
            }
        }
    }



    public function createOrderFromBid($bid, $highestBidder)
    {
        try {
            Log::info('Starting order creation process.', ['bidId' => $bid->id]);

            // إعداد القيم المطلوبة لإنشاء الطلب
            $orderData = [
                'user_id' => $highestBidder->id, // معرف المشتري
                'order_number' => uniqid('ORDER-'),
                'order_status' => 'Pending',
                'buyer_name' => $highestBidder->name,
                'buyer_email' => $highestBidder->email ?? 'غير متوفر',
                'buyer_phone' => $highestBidder->phone ?? 'غير متوفر',
                'buyer_address' => $highestBidder->pivot->shipping_address ?? 'غير متوفر',
                'buyer_city' => $bid->region ?? 'غير متوفر',
                'total' => $highestBidder->pivot->bid_amount,
                'bid_id' => $bid->id, // تعيين bid_id عند إنشاء الطلب
                'seller_id' => $bid->user_id, // تعيين seller_id عند إنشاء الطلب
            ];

            // إنشاء الطلب باستخدام بيانات المزاد
            $order = Orders::create($orderData);

            Log::info('Order created successfully.', ['orderId' => $order->id]);

            // إضافة عنصر الطلب المرتبط بالمزاد
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $bid->id,
                'product_name' => $bid->product_name,
                'quantity' => 1,
                'price' => $highestBidder->pivot->bid_amount,
            ]);

            // تحديث حالة المزايدة إلى "مباع"
            $bid->status = 'sold';
            $bid->save();

            Log::info('Bid status updated to "sold".', ['bidId' => $bid->id]);

            // إرسال إشعار إلى البائع عند تحويل المزايدة إلى طلب
            $this->sendNotificationToSeller($bid, 'تم تحويل المزايدة الخاصة بك إلى طلب');

            return $order;
        } catch (\Exception $e) {
            Log::error('Exception while creating order from bid:', ['message' => $e->getMessage(), 'bidId' => $bid->id]);
            return null;
        }
    }




    protected function sendNotificationToSeller($bid, $message)
    {
        try {
            $sellerFcmToken = $bid->fcm_token; // جلب fcm_token الخاص بالبائع

            if ($sellerFcmToken) {
                Log::info('Sending notification to seller.', [
                    'bid_id' => $bid->id,
                    'fcm_token' => $sellerFcmToken,
                    'message' => $message,
                ]);

                // إرسال الإشعار باستخدام `OneSignal`
                $bid->notify(new BidNotification($message));
            } else {
                Log::warning('Seller FCM token is missing. Notification not sent.', [
                    'bid_id' => $bid->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification to seller.', [
                'bid_id' => $bid->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function convertBidToOrder(Request $request, $bidId)
    {
        try {
            // البحث عن المزايدة
            $bid = Bids::with(['user', 'bidders', 'category', 'subcategory'])->findOrFail($bidId);

            // التحقق من حالة المزايدة
            if ($bid->status !== 'pending' && $bid->status !== 'sold') {
                return response()->json(['message' => 'المزايدة لا يمكن تحويلها إلى طلب'], 400);
            }

            // الحصول على المزايد الأعلى
            $highestBidder = $bid->bidders()->orderBy('bid_amount', 'desc')->first();

            if (!$highestBidder) {
                return response()->json(['message' => 'لا يوجد مزايدين على هذه المزايدة'], 400);
            }

            // إنشاء الطلب باستخدام دالة `createOrderFromBid`
            $order = $this->createOrderFromBid($bid, $highestBidder);

            if (!$order) {
                return response()->json(['message' => 'فشل في إنشاء الطلب من المزايدة'], 500);
            }

            // جلب الطلب مع جميع البيانات ذات الصلة
            $order->load(['items', 'seller', 'user']);

            // إعداد البيانات للاستجابة
            $orderData = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'buyer_name' => $order->buyer_name,
                'buyer_email' => $order->buyer_email,
                'buyer_phone' => $order->buyer_phone,
                'buyer_address' => $order->buyer_address,
                'buyer_city' => $order->buyer_city,
                'total' => $order->total,
                'seller_info' => $order->seller ? [
                    'name' => $order->seller->name,
                    'email' => $order->seller->email,
                    'phone' => $order->seller->phone,
                ] : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
                'bid_info' => [
                    'product_name' => $bid->product_name,
                    'initial_price' => $bid->initial_price,
                    'current_price' => $bid->current_price,
                    'end_time' => $bid->end_time,
                    'category' => $bid->category->name ?? null,
                    'subcategory' => $bid->subcategory->name ?? null,
                    'status' => $bid->status,
                ]
            ];

            return response()->json([
                'message' => 'تم تحويل المزايدة إلى طلب بنجاح',
                'order' => $orderData
            ], 200);

        } catch (\Exception $e) {
            Log::error('Exception while converting bid to order:', ['message' => $e->getMessage(), 'bidId' => $bidId]);
            return response()->json(['message' => 'حدث خطأ أثناء تحويل المزايدة إلى طلب'], 500);
        }
    }

    public function placeBid(Request $request, $bidId)
    {
        $bid = Bids::findOrFail($bidId);
        $bidAmount = $request->input('bid_amount');

        // التحقق إذا كان السعر الحالي يساوي السعر الذي حدده البائع
        if ($bidAmount >= $bid->initial_price) {
            $bid->status = 'sold';
            $bid->buyer_id = Auth::user()->id;
            $bid->current_price = $bidAmount;
            $bid->end_time = now();
            $bid->save();

            // تحويل المزاد إلى طلب
            $this->createOrderFromBid($bid, Auth::user());

            // إرسال إشعار إلى البائع
            $this->sendNotificationToSeller($bid, 'تم بيع المزايدة بنجاح');

            return response()->json(['message' => 'تم البيع مباشرة بسبب المطابقة مع السعر المحدد من قبل البائع']);
        }

        // إضافة العرض الجديد
        $bid->bidders()->syncWithoutDetaching([
            Auth::user()->id => [
                'bid_amount' => $bidAmount,
                'shipping_address' => $request->input('shipping_address'),
                'phone_id' => $request->input('phone_id'),
            ],
        ]);

        // تحديث السعر الحالي في المزاد
        $bid->current_price = $bidAmount;
        $bid->save();

        // تسجيل Log للتحقق من استدعاء sendNotificationToSeller
        Log::info('Attempting to send notification on bid update.', ['bid_id' => $bid->id, 'fcm_token' => $bid->fcm_token]);

        // إرسال إشعار إلى البائع بتحديث السعر
        $this->sendNotificationToSeller($bid, 'تم تقديم عرض جديد على المزايدة الخاصة بك');

        return response()->json(['message' => 'تم تقديم العرض بنجاح']);
    }



    public function getUserBids($userId)
    {
        try {
            $bids = Bids::where('user_id', $userId)->get();
            if ($bids->isEmpty()) {
                return response()->json(['message' => 'لم يتم العثور على مزادات لهذا المستخدم'], 404);
            }
            foreach ($bids as $bid) {
                // تعديل مسارات الصور لتعمل بشكل صحيح على سي بانل
                if (!empty($bid->product_image1)) {
                    if (str_starts_with($bid->product_image1, '/storage/')) {
                        $bid->product_image1 = asset(substr($bid->product_image1, 1));
                    } else {
                        $bid->product_image1 = asset('storage/' . $bid->product_image1);
                    }
                }
                if (!empty($bid->product_image2)) {
                    if (str_starts_with($bid->product_image2, '/storage/')) {
                        $bid->product_image2 = asset(substr($bid->product_image2, 1));
                    } else {
                        $bid->product_image2 = asset('storage/' . $bid->product_image2);
                    }
                }
                if (!empty($bid->product_image3)) {
                    if (str_starts_with($bid->product_image3, '/storage/')) {
                        $bid->product_image3 = asset(substr($bid->product_image3, 1));
                    } else {
                        $bid->product_image3 = asset('storage/' . $bid->product_image3);
                    }
                }
                if (!empty($bid->product_image4)) {
                    if (str_starts_with($bid->product_image4, '/storage/')) {
                        $bid->product_image4 = asset(substr($bid->product_image4, 1));
                    } else {
                        $bid->product_image4 = asset('storage/' . $bid->product_image4);
                    }
                }
                if (!empty($bid->product_image5)) {
                    if (str_starts_with($bid->product_image5, '/storage/')) {
                        $bid->product_image5 = asset(substr($bid->product_image5, 1));
                    } else {
                        $bid->product_image5 = asset('storage/' . $bid->product_image5);
                    }
                }

                // تضمين خيار التوصيل في الاستجابة
                $bid->shipping_option_text = $bid->shipping_option === 'seller' ? 'التوصيل على البائع' : 'التوصيل على المشتري';

                // تضمين الألوان والأحجام المتاحة
                $bid->available_colors_text = is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null;
                $bid->available_sizes_text = is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null;
            }
            return response()->json($bids);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'لم يتم العثور على المزادات'], 404);
        }
    }

    public function getBidsByCategory($categoryId)
    {
        try {
            $bids = Bids::where('category_id', $categoryId)->get();
            if ($bids->isEmpty()) {
                return response()->json(['message' => 'لم يتم العثور على مزادات لهذه الفئة'], 404);
            }
            foreach ($bids as $bid) {
                // تعديل مسارات الصور لتعمل بشكل صحيح على سي بانل
                if (!empty($bid->product_image1)) {
                    if (str_starts_with($bid->product_image1, '/storage/')) {
                        $bid->product_image1 = asset(substr($bid->product_image1, 1));
                    } else {
                        $bid->product_image1 = asset('storage/' . $bid->product_image1);
                    }
                }
                if (!empty($bid->product_image2)) {
                    if (str_starts_with($bid->product_image2, '/storage/')) {
                        $bid->product_image2 = asset(substr($bid->product_image2, 1));
                    } else {
                        $bid->product_image2 = asset('storage/' . $bid->product_image2);
                    }
                }
                if (!empty($bid->product_image3)) {
                    if (str_starts_with($bid->product_image3, '/storage/')) {
                        $bid->product_image3 = asset(substr($bid->product_image3, 1));
                    } else {
                        $bid->product_image3 = asset('storage/' . $bid->product_image3);
                    }
                }
                if (!empty($bid->product_image4)) {
                    if (str_starts_with($bid->product_image4, '/storage/')) {
                        $bid->product_image4 = asset(substr($bid->product_image4, 1));
                    } else {
                        $bid->product_image4 = asset('storage/' . $bid->product_image4);
                    }
                }
                if (!empty($bid->product_image5)) {
                    if (str_starts_with($bid->product_image5, '/storage/')) {
                        $bid->product_image5 = asset(substr($bid->product_image5, 1));
                    } else {
                        $bid->product_image5 = asset('storage/' . $bid->product_image5);
                    }
                }

                // تضمين الألوان والأحجام المتاحة
                $bid->available_colors_text = is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null;
                $bid->available_sizes_text = is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null;
            }
            return response()->json($bids);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'لم يتم العثور على المزادات'], 404);
        }
    }

    public function getCategories()
    {
        $categories = Category::all(['id', 'name']);
        return response()->json($categories);
    }
    public function getBidsEndingSoon($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case '6hours':
                $end = $now->copy()->addHours(6);
                break;
            case '12hours':
                $end = $now->copy()->addHours(12);
                break;
            case '1day':
                $end = $now->copy()->addDay();
                break;
            case '2days':
                $end = $now->copy()->addDays(2);
                break;
            default:
                return response()->json(['message' => 'تم تحديد فترة غير صالحة'], 400);
        }

        $bids = Bids::whereBetween('end_time', [$now, $end])
            ->orderBy('end_time', 'asc')
            ->get();

        if ($bids->isEmpty()) {
            return response()->json(['message' => 'لا توجد مزادات تنتهي قريبًا'], 404);
        }

        return response()->json($bids);
    }

    public function updateCurrentPrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric',
            'shipping_address' => 'required|string|max:255',
            'phone_id' => 'required|string|max:255', // إضافة التحقق من phone_id
        ]);

        try {
            $bid = Bids::findOrFail($id);

            if ($request->current_price > $bid->current_price) {
                $bid->current_price = $request->current_price;
                $bid->shipping_address = $request->shipping_address; // حفظ عنوان التوصيل

                if ($bid->save()) {
                    // استخدام syncWithoutDetaching لتحديث أو إضافة السجل في جدول pivot مع phone_id
                    $bid->bidders()->syncWithoutDetaching([
                        Auth::user()->id => [
                            'bid_amount' => $request->current_price,
                            'shipping_address' => $request->shipping_address, // تأكد من إرسال هذا الحقل
                            'phone_id' => $request->phone_id,
                        ],
                    ]);

                    return response()->json(['message' => 'تم تحديث السعر الحالي بنجاح'], 200);
                } else {
                    return response()->json(['message' => 'فشل في تحديث السعر الحالي'], 500);
                }
            }

            return response()->json(['message' => 'يجب أن يكون السعر الجديد أعلى من السعر الحالي'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'لم يتم العثور على المزاد'], 404);
        }
    }


    public function getActiveBids(Request $request)
    {
        $user = $request->user();

        $bids = Bids::where('user_id', $user->id)
            ->where('end_time', '>', now())
            ->get(['id', 'product_name', 'product_description', 'product_image1', 'end_time', 'initial_price', 'current_price']);

        foreach ($bids as $bid) {
            // تعديل مسار الصورة ليعمل بشكل صحيح على سي بانل
            if (!empty($bid->product_image1)) {
                if (str_starts_with($bid->product_image1, '/storage/')) {
                    $bid->product_image1 = asset(substr($bid->product_image1, 1));
                } else {
                    $bid->product_image1 = asset('storage/' . $bid->product_image1);
                }
            }
        }

        return response()->json($bids);
    }

    public function getUserBidOffers(Request $request)
    {
        $userId = $request->user()->id;

        $bids = Bids::where('user_id', $userId)
            ->whereHas('bidders')
            ->with(['bidders' => function($query) {
                $query->select('users.id', 'users.name', 'users.phone')
                    ->withPivot('bid_amount', 'shipping_address', 'status', 'phone_id')
                    ->orderBy('bid_user.bid_amount', 'desc');
            }])
            ->get(['id', 'product_name', 'initial_price', 'current_price', 'end_time', 'available_colors', 'available_sizes']);

        if ($bids->isEmpty()) {
            return response()->json(['message' => 'لم يتم العثور على عروض للمزايدات الخاصة بك'], 404);
        }

        $offers = $bids->map(function($bid) {
            $highestBidder = $bid->bidders->first();
            return [
                'bid_id' => $bid->id,
                'product_name' => $bid->product_name,
                'initial_price' => $bid->initial_price,
                'current_price' => $bid->current_price,
                'end_time' => $bid->end_time,
                'highest_offer' => [
                    'buyer_name' => $highestBidder->name,
                    'buyer_phone' => $highestBidder->phone ?? 'غير متوفر',
                    'offered_price' => $highestBidder->pivot->bid_amount,
                    'shipping_address' => $highestBidder->pivot->shipping_address ?? 'غير متوفر',
                    'phone_id' => $highestBidder->pivot->phone_id ?? 'غير متوفر',
                    'bidder_id' => $highestBidder->id
                ],
                'status' => $bid->status, // تعديل هذا السطر لعرض حالة المزايدة الرئيسية
                'available_colors' => $bid->available_colors,
                'available_sizes' => $bid->available_sizes,
                'available_colors_text' => is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null,
                'available_sizes_text' => is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null
            ];
        });

        return response()->json($offers);
    }

    public function respondToBidOffer(Request $request, $bidId, $bidderId)
    {
        $request->validate([
            'response' => 'required|in:accept,reject'
        ]);

        $user = $request->user();

        // البحث عن المزايدة باستخدام bid_id و user_id للتحقق
        $bid = Bids::where('id', $bidId)->where('user_id', $user->id)->first();

        // التحقق مما إذا كانت المزايدة موجودة
        if (!$bid) {
            Log::error('Bid not found:', ['bidId' => $bidId]);
            return response()->json(['message' => 'لم يتم العثور على المزايدة'], 404);
        }

        $bidder = $bid->bidders()->where('users.id', $bidderId)->firstOrFail();

        // تحديث حالة المزايدة بناءً على الاستجابة
        if ($request->response === 'accept') {
            $bid->status = 'accepted';
            $bid->buyer_id = $bidderId;
            $bid->current_price = $bidder->pivot->bid_amount;
            $bid->end_time = now();

            // تحديث حالة العارض في جدول الربط
            $bid->bidders()->updateExistingPivot($bidderId, ['status' => 'accepted']);
            $message = 'تم قبول العرض بنجاح وإنهاء المزاد';
        } else {
            $bid->status = 'rejected'; // تحديث حالة المزايدة إلى مرفوضة
            // تحديث حالة العارض في جدول الربط
            $bid->bidders()->updateExistingPivot($bidderId, ['status' => 'rejected']);
            $message = 'تم رفض العرض بنجاح';
        }

        // حفظ حالة المزايدة في جميع الحالات
        if (!$bid->save()) {
            Log::error('Failed to update bid status in bids table:', ['bidId' => $bidId, 'status' => $bid->status]);
            return response()->json(['message' => 'فشل في تحديث حالة المزايدة'], 500);
        }

        // تعديل مسار الصورة ليعمل بشكل صحيح على سي بانل
        $productImage = '';
        if (!empty($bid->product_image1)) {
            if (str_starts_with($bid->product_image1, '/storage/')) {
                $productImage = asset(substr($bid->product_image1, 1));
            } else {
                $productImage = asset('storage/' . $bid->product_image1);
            }
        }

        $bidDetails = [
            'product_name' => $bid->product_name,
            'product_image' => $productImage,
            'buyer_bid_amount' => $bidder->pivot->bid_amount,
            'initial_price' => $bid->initial_price,
            'current_price' => $bid->current_price,
            'shipping_address' => $bidder->shipping_address ?? 'لم يتم تحديد العنوان',
            'seller_name' => $user->name,
            'seller_phone' => $user->phone,
            'status' => $bid->status, // عرض الحالة من المزايدة الرئيسية
            'end_time' => $bid->end_time,
            'available_colors' => $bid->available_colors,
            'available_sizes' => $bid->available_sizes,
            'available_colors_text' => is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null,
            'available_sizes_text' => is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null
        ];

        Log::info('Bid status updated successfully:', ['bidId' => $bidId, 'status' => $bid->status]);

        return response()->json([
            'message' => $message,
            'bid_details' => $bidDetails
        ]);
    }




    public function getBuyerBidDetails(Request $request)
    {
        $user = $request->user();

        $bids = Bids::whereHas('bidders', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })->with(['user:id,name,phone', 'bidders' => function($query) use ($user) {
            $query->where('users.id', $user->id);
        }])->get();

        $bidDetails = $bids->map(function($bid) use ($user) {
            $buyerBid = $bid->bidders->first();

            // تعديل مسار الصورة ليعمل بشكل صحيح على سي بانل
            $productImage = '';
            if (!empty($bid->product_image1)) {
                if (str_starts_with($bid->product_image1, '/storage/')) {
                    $productImage = asset(substr($bid->product_image1, 1));
                } else {
                    $productImage = asset('storage/' . $bid->product_image1);
                }
            }

            return [
                'product_name' => $bid->product_name,
                'product_image' => $productImage,
                'buyer_bid_amount' => $buyerBid->pivot->bid_amount,
                'initial_price' => $bid->initial_price,
                'current_price' => $bid->current_price,
                'shipping_address' => $user->shipping_address ?? 'لم يتم تحديد العنوان',
                'seller_name' => $bid->user->name,
                'seller_phone' => $bid->user->phone,
                'status' => $this->getBidStatus($bid, $buyerBid),
                'end_time' => $bid->end_time,
                'available_colors' => $bid->available_colors,
                'available_sizes' => $bid->available_sizes,
                'available_colors_text' => is_array($bid->available_colors) ? implode(', ', $bid->available_colors) : null,
                'available_sizes_text' => is_array($bid->available_sizes) ? implode(', ', $bid->available_sizes) : null
            ];
        });

        return response()->json($bidDetails);
    }

    private function getBidStatus($bid, $buyerBid)
    {
        if ($bid->status === 'accepted' && $bid->buyer_id === $buyerBid->id) {
            return 'مقبول (قيد التوصيل)';
        } elseif ($buyerBid->pivot->status === 'rejected') {
            return 'مرفوض';
        } elseif ($bid->end_time > now()) {
            return 'قيد الانتظار';
        } else {
            return 'منتهي';
        }
    }

    public function getUserActiveBids(Request $request)
    {
        $user = $request->user();

        $activeBids = Bids::whereHas('bidders', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->where('end_time', '>', now())
            ->with(['bidders' => function ($query) use ($user) {
                $query->where('users.id', $user->id)->select('users.id', 'bid_user.bid_amount');
            }])
            ->get(['id', 'product_name', 'product_description', 'product_image1', 'end_time', 'initial_price', 'current_price']);

        $activeBids = $activeBids->map(function ($bid) {
            // تعديل مسار الصورة ليعمل بشكل صحيح على سي بانل
            if (!empty($bid->product_image1)) {
                if (str_starts_with($bid->product_image1, '/storage/')) {
                    $bid->product_image1 = asset(substr($bid->product_image1, 1));
                } else {
                    $bid->product_image1 = asset('storage/' . $bid->product_image1);
                }
            }
            $bid->user_bid_amount = $bid->bidders->first()->pivot->bid_amount;
            unset($bid->bidders);
            return $bid;
        });

        return response()->json($activeBids);
    }
}
