<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;


Route::redirect('/', '/login');

Route::get('orders/{id}/export-pdf', [OrdersController::class, 'exportOrderPdf']);
