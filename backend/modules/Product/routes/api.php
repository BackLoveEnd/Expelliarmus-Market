<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Controllers\Product\ManipulationProductController;
use Modules\Product\Http\Management\Controllers\Product\ProductSpecificationsController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;
use Modules\Product\Http\Management\Controllers\Images\ProductImagesController;

//TODO: guards

Route::prefix('management')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/{productBind}/staff', [RetrieveProductController::class, 'getProductStaffInfo'])
            ->whereNumber('productBind');

        Route::get('/{productBind}/{slug}', [RetrieveProductController::class, 'show'])
            ->whereNumber('productBind');

        Route::post('/create', [ManipulationProductController::class, 'store']);

        Route::put('/{product}', [ManipulationProductController::class, 'edit'])
            ->whereNumber('productBind');
    });

    Route::get(
        uri: '/product-specifications/category/{category}',
        action: [ProductSpecificationsController::class, 'getSpecsByCategory']
    )
        ->whereNumber('category');

    Route::post('/product/{product}/images', [ProductImagesController::class, 'store']);

    Route::post('/product/{product}/images/edit', [ProductImagesController::class, 'edit']);
});