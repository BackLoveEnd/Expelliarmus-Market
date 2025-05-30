<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Management\Controllers\CategoryController;
use Modules\Category\Http\Management\Controllers\CategoryIconController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;

Route::prefix('management')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::controller(RetrieveProductController::class)->middleware('auth.manager')->group(function () {
            Route::get('/products', 'getProductsByRootCategories');

            Route::get('/{category}/products', 'getProductsByCategory')
                ->whereNumber('category');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/', 'index')->withoutMiddleware(['throttle:api', 'auth.manager']);

            Route::get('/root', 'rootCategories');

            Route::post('/', 'create')->middleware('auth.manager');

            Route::whereNumber('category')->group(function () {
                Route::get('/{category}/attributes', 'getAllAttributesForCategory')->middleware('auth.manager');

                Route::put('/{category}', 'edit')->middleware('auth.manager');

                Route::delete('/{category}', 'delete')->middleware('auth.manager:super-manager');

                Route::delete('/{category}/attributes/{attribute}', 'deleteAttribute')->middleware(
                    'auth.manager:super-manager',
                );
            });
        });

        Route::whereNumber('category')
            ->middleware('auth.manager')
            ->controller(CategoryIconController::class)
            ->group(
                function () {
                    Route::post('/{category}/icon', 'uploadIcon');

                    Route::post('/{category}/icon', 'editIcon');
                },
            );
    });
});
