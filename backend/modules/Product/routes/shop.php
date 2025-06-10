<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Shop\Controllers\DiscountedProductsController;
use Modules\Product\Http\Shop\Controllers\MinMaxPricesProductController;
use Modules\Product\Http\Shop\Controllers\ProductListingController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductController;
use Modules\Product\Http\Shop\Controllers\RetrieveProductsController;

Route::prefix('/shop')->withoutMiddleware(['throttle:api'])->group(function () {
    Route::controller(DiscountedProductsController::class)->group(function () {
        Route::get('/home/products/flash-sales', 'getFlashSales');
    });

    Route::controller(ProductListingController::class)->group(function () {
        Route::get('/home/products/explore', 'explore');

        Route::get('/products/suggestions', 'suggestions');

        Route::get('/products/top-sellers', 'topSellers');

        Route::get('/categories/{category:slug}/products', 'relatedToProduct');
    });

    Route::get('/products', [RetrieveProductsController::class, 'index']);

    Route::controller(MinMaxPricesProductController::class)->group(function () {
        Route::get('/products/staff/prices-range', 'getMinMaxProductsPrice');

        Route::get('/products/categories/{category:slug}/staff/prices-range', 'getMinMaxProductsPriceForCategory');

        Route::get('/products/brands/{brand:slug}/staff/prices-range', 'getMinMaxProductsPriceForBrand');
    });

    Route::get('/products/{productBind}/{slug?}', RetrieveProductController::class)
        ->middleware(['include:category,warehouse,brand,variations'])
        ->whereNumber('productBind');
});
