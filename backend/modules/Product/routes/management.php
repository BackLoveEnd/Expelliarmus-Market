<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Management\Controllers\Images\ProductImagesController;
use Modules\Product\Http\Management\Controllers\Product\ManipulationProductController;
use Modules\Product\Http\Management\Controllers\Product\ProductSpecificationsController;
use Modules\Product\Http\Management\Controllers\Product\ProductStatusController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;
use Modules\Product\Http\Management\Controllers\Product\TrashedProductsController;

Route::prefix('management')->group(function () {
    Route::prefix('products')->group(function () {
        Route::controller(RetrieveProductController::class)->middleware('auth.manager')->group(function () {
            Route::get('/{productBind}/staff', 'getProductStaffInfo')
                ->whereNumber('productBind');

            Route::get('/{productBind}/{slug}', 'show')
                ->middleware(['include:category,brand,variations'])
                ->whereNumber('productBind');
        });

        Route::controller(ManipulationProductController::class)->middleware('auth.manager')->group(function () {
            Route::post('/create', 'store');

            Route::put('/{product}', 'edit')
                ->whereNumber('productBind');
        });

        Route::controller(ProductStatusController::class)->middleware('auth.manager')->group(function () {
            Route::post('/{product}/publish', 'publish')->whereNumber('product');

            Route::post('/{product}/unpublish', 'unPublish')->whereNumber('product');
        });

        Route::controller(TrashedProductsController::class)->group(function () {
            Route::get('/trashed', 'getTrashed')->middleware('auth.manager');

            Route::post('/{product}/restore', 'restore')
                ->middleware('auth.manager')
                ->whereNumber('product')
                ->withTrashed();

            Route::delete('/{product}/trash', 'moveToTrash')
                ->middleware('auth.manager')
                ->whereNumber('product');

            Route::delete('/{product}', 'deleteForever')
                ->middleware('auth.manager:super-manager')
                ->whereNumber('product')
                ->withTrashed();
        });
    });

    Route::get(
        uri: '/product-specifications/category/{category}',
        action: [ProductSpecificationsController::class, 'getSpecsByCategory'],
    )
        ->middleware('auth.manager')
        ->whereNumber('category');

    Route::post('/products/{product}/images', [ProductImagesController::class, 'store'])->middleware('auth.manager');

    Route::post('/products/{product}/images/edit', [ProductImagesController::class, 'edit'])->middleware(
        'auth.manager',
    );
});
