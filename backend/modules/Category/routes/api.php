<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Management\Controllers\CategoryController;
use Modules\Category\Http\Management\Controllers\CategoryIconController;
use Modules\Category\Http\Shop\Controllers\ShopCategoryAccessController;
use Modules\Product\Http\Management\Controllers\Product\RetrieveProductController;

Route::prefix('management')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::controller(RetrieveProductController::class)->group(function () {
            Route::get('/products', 'getProductsByRootCategories');

            Route::get('/{category}/products', 'getProductsByCategory')
                ->whereNumber('category');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/', 'index');

            Route::get('/root', 'rootCategories');

            Route::post('/', 'create');

            Route::whereNumber('category')->group(function () {
                Route::get('/{category}/attributes', 'getAllAttributesForCategory');

                Route::put('/{category}', 'edit');

                Route::delete('/{category}', 'delete');

                Route::delete('/{category}/attributes/{attribute}', 'deleteAttribute');
            });
        });

        Route::whereNumber('category')->controller(CategoryIconController::class)->group(function () {
            Route::post('/{category}/icon', 'uploadIcon');

            Route::post('/{category}/icon', 'editIcon');
        });
    });
});

Route::prefix('shop')->group(function () {
    Route::get('/categories/browse', [ShopCategoryAccessController::class, 'getCategoriesBrowseList']);
});
