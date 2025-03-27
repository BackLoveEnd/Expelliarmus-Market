<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Shop\Controllers\DiscountedProductsController;
use Modules\Product\Http\Shop\Controllers\ProductListingController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductsController;

Route::prefix('/shop')->withoutMiddleware(['throttle'])->group(function () {
    Route::controller(DiscountedProductsController::class)->group(function () {
        Route::get('/home/products/flash-sales', 'getFlashSales');
    });

    Route::controller(ProductListingController::class)->group(function () {
        Route::get('/home/products/explore', 'explore');

        Route::get('/categories/{category:slug}/products', 'relatedToProduct');
    });

    Route::get('/products', [RetrieveProductsController::class, 'index']);

    Route::get('/products/{productBind}/{slug?}', RetrieveProductController::class)
        ->middleware(['include:category,warehouse,brand,variations'])
        ->whereNumber('productBind');
});