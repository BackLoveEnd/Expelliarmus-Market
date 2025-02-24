<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Controllers\Images\ProductImagesController;
use Modules\Product\Http\Management\Controllers\Product\ManipulationProductController;
use Modules\Product\Http\Management\Controllers\Product\ProductSpecificationsController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;

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

        Route::delete('/{product}/trash', [ManipulationProductController::class, 'moveToTrash'])
            ->whereNumber('product');
    });

    Route::get(
        uri: '/product-specifications/category/{category}',
        action: [ProductSpecificationsController::class, 'getSpecsByCategory'],
    )
        ->whereNumber('category');

    Route::post('/product/{product}/images', [ProductImagesController::class, 'store']);

    Route::post('/product/{product}/images/edit', [ProductImagesController::class, 'edit']);
});