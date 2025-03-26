<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Shop\Controllers\DiscountedProductsController;
use Modules\Product\Http\Shop\Controllers\HomePageProductsController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductsController;

Route::prefix('/shop')->withoutMiddleware(['throttle'])->group(function () {
    Route::controller(DiscountedProductsController::class)->group(function () {
        Route::get('/home/products/flash-sales', 'getFlashSales');
    });

    Route::controller(HomePageProductsController::class)->group(function () {
        Route::get('/home/products/explore', 'explore');
    });

    Route::get('/products', [RetrieveProductsController::class, 'index']);

    Route::get('/products/{productBind}/{slug?}', RetrieveProductController::class)
        ->middleware(['include:category,warehouse,brand,variations'])
        ->whereNumber('productBind');
});