<?php

use App\Http\Controllers\HomeBannerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BidsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AddressController;



// مسارات المصادقة
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/update-profile', [UsersController::class, 'updateProfile']); // مسار تحديث بيانات المستخدم

    // مسارات طرق الدفع
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
    Route::get('/payment-methods/{id}', [PaymentMethodController::class, 'show']);
    Route::put('/payment-methods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy']);
});

// مسارات البانرات
Route::get('/banners', [BannerController::class, 'index']);

// مسارات الطلبات
Route::post('/orders', [OrdersController::class, 'store']);
Route::get('user-orders/{userId}', [OrdersController::class, 'userOrders']);
Route::get('/user/orders/{userId}', [OrdersController::class, 'userOrders']);
Route::put('/orders/{orderId}/update-ids', [OrdersController::class, 'updateOrderIds']);
Route::post('/orders/{orderId}/rate', [OrderController::class, 'rateSeller'])->middleware('auth:api');
Route::get('orders/{id}/export-pdf', [OrdersController::class, 'exportOrderPdf']);
Route::post('/orders/{orderId}/update-status', [OrdersController::class, 'updateOrderStatus']);
Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');



// مسارات الفئات
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
Route::get('/categories/{categoryId}/sub-categories', [CategoryController::class, 'getSubCategories']);
Route::get('/categories-with-subcategories', [CategoryController::class, 'getAllCategoriesWithSubcategories']);


// مسارات المزايدات
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/new/bids', [BidsController::class, 'store']);
    Route::get('/bids', [BidsController::class, 'index']); // يمكن استخدامه الآن مع ?sort=asc أو ?sort=desc
    Route::get('/bids/user/{userId}', [BidsController::class, 'getUserBids']);
    Route::patch('/bids/{id}/update-current-price', [BidsController::class, 'updateCurrentPrice']);
    Route::get('/user/active-bids', [BidsController::class, 'getUserActiveBids']);
    Route::get('/bids/user/{userId}/orders', [BidsController::class, 'getUserBidsOrders']);
    Route::post('/bids/{bidId}/convert-to-order', [BidsController::class, 'convertBidToOrder']);

});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('addresses', [AddressController::class, 'index']);
    Route::post('addresses', [AddressController::class, 'store']);
    Route::put('addresses/{address}', [AddressController::class, 'update']);
    Route::delete('addresses/{address}', [AddressController::class, 'destroy']);
});

Route::get('/bids/category/{categoryId}', [BidsController::class, 'getBidsByCategory']);
Route::get('/bids/ending-soon/{period}', [BidsController::class, 'getBidsEndingSoon']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/bid-offers', [BidsController::class, 'getUserBidOffers']);
    Route::post('/user/bid-offers/{bidId}/{bidderId}/respond', [BidsController::class, 'respondToBidOffer']);
    Route::get('/user/bid-details', [BidsController::class, 'getBuyerBidDetails']);
});
Route::get('/home-banners', [HomeBannerController::class, 'getHomeBanners']);
Route::post('/home-banners', [HomeBannerController::class, 'store']);

// مسارات الخصائص
Route::get('/properties', [PropertyController::class, 'getActiveProperties']);
Route::get('/properties/{id}', [PropertyController::class, 'getProperty']);

