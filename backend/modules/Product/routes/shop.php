<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Shop\Controllers\DiscountedProductsController;
use Modules\Product\Http\Shop\Controllers\HomePageProductsController;

Route::prefix('/shop')->group(function () {
    Route::controller(DiscountedProductsController::class)->group(function () {
        Route::get('/home/products/flash-sales', 'getFlashSales');
    });

    Route::controller(HomePageProductsController::class)->group(function () {
        Route::get('/home/products/explore', 'explore');
    });
});