<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BidsController;




Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/logout', [AuthController::class, 'logout']);


Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/category/{category}', [ProductsController::class, 'getByCategory']);


Route::get('/banners', [BannerController::class, 'index']);

Route::post('/bids', [BidsController::class, 'store']);
Route::get('/bids', [BidsController::class, 'index']);
Route::get('/bids/user/{user_id}', [BidsController::class, 'getUserBids']);


Route::post('/orders', [OrdersController::class, 'store']);
