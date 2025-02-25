<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Controllers\Images\ProductImagesController;
use Modules\Product\Http\Management\Controllers\Product\ManipulationProductController;
use Modules\Product\Http\Management\Controllers\Product\ProductSpecificationsController;
use Modules\Product\Http\Management\Controllers\Product\ProductStatusController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;
use Modules\Product\Http\Management\Controllers\Product\TrashedProductsController;

//TODO: guards

Route::prefix('management')->group(function () {
    Route::prefix('products')->group(function () {
        Route::controller(RetrieveProductController::class)->group(function () {
            Route::get('/{productBind}/staff', 'getProductStaffInfo')
                ->whereNumber('productBind');

            Route::get('/{productBind}/{slug}', 'show')
                ->whereNumber('productBind');
        });

        Route::controller(ManipulationProductController::class)->group(function () {
            Route::post('/create', 'store');

            Route::put('/{product}', 'edit')
                ->whereNumber('productBind');

            Route::delete('/{product}/trash', 'moveToTrash')
                ->whereNumber('product');
        });

        Route::controller(ProductStatusController::class)->group(function () {
            Route::post('/{product}/publish', 'publish')->whereNumber('product');

            Route::post('/{product}/unpublish', 'unPublish')->whereNumber('product');
        });

        Route::controller(TrashedProductsController::class)->group(function () {
            Route::get('/trashed', 'getTrashed');
        });
    });

    Route::get(
        uri: '/product-specifications/category/{category}',
        action: [ProductSpecificationsController::class, 'getSpecsByCategory'],
    )
        ->whereNumber('category');

    Route::post('/products/{product}/images', [ProductImagesController::class, 'store']);

    Route::post('/products/{product}/images/edit', [ProductImagesController::class, 'edit']);
});