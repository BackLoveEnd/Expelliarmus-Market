<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Controllers\ManipulationProductController;
use Modules\Product\Http\Management\Controllers\ProductImagesController;
use Modules\Product\Http\Management\Controllers\ProductSpecificationsController;
use Modules\Product\Http\Management\Controllers\RetrieveProductController;

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
});