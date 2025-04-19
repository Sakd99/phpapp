<?php

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\SubCategoryResource;
use App\Filament\Resources\SubSubCategoryResource;

// تأكد من أن لديك المسارات التالية
Route::middleware(['auth'])
    ->group(function () {
        Route::get('/categories', [CategoryResource::class, 'index'])->name('filament.resources.categories.index');
        Route::get('/categories/create', [CategoryResource::class, 'create'])->name('filament.resources.categories.create');
        Route::get('/categories/{record}/edit', [CategoryResource::class, 'edit'])->name('filament.resources.categories.edit');

        Route::get('/sub-categories/create', [SubCategoryResource::class, 'create'])->name('filament.resources.sub-categories.create');
        Route::get('/sub-categories', [SubCategoryResource::class, 'index'])->name('filament.resources.sub-categories.index');
        Route::get('/sub-categories/{record}/edit', [SubCategoryResource::class, 'edit'])->name('filament.resources.sub-categories.edit');

        Route::get('/sub-sub-categories/create', [SubSubCategoryResource::class, 'create'])->name('filament.resources.sub-sub-categories.create');
        Route::get('/sub-sub-categories', [SubSubCategoryResource::class, 'index'])->name('filament.resources.sub-sub-categories.index');
        Route::get('/sub-sub-categories/{record}/edit', [SubSubCategoryResource::class, 'edit'])->name('filament.resources.sub-sub-categories.edit');
    });
