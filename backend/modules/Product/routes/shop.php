<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Shop\Controllers\DiscountedProductsController;
use Modules\Product\Http\Shop\Controllers\HomePageProductsController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductsController;

Route::prefix('/shop')->withoutMiddleware(['throttle'])->group(function () {
    Route::controller(DiscountedProductsController::class)->group(function () {
        Route::get('/home/products/flash-sales', 'getFlashSales');
    });

    Route::controller(HomePageProductsController::class)->group(function () {
        Route::get('/home/products/explore', 'explore');
    });

    Route::get('/products', [RetrieveProductsController::class, 'index']);
});