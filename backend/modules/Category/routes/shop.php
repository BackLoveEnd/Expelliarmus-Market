<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Shop\Controllers\CategoriesBrandController;
use Modules\Category\Http\Shop\Controllers\CategoryOptionAttributesController;
use Modules\Category\Http\Shop\Controllers\ShopCategoryAccessController;

Route::prefix('shop')->withoutMiddleware(['throttle:api'])->group(function () {
    Route::get('/home/categories/browse', [ShopCategoryAccessController::class, 'getCategoriesBrowseList']);

    Route::get('/categories/{category:slug}/children', [ShopCategoryAccessController::class, 'getChildrenOfCategory']);

    Route::get(
        '/categories/{categorySlug}/attributes',
        [CategoryOptionAttributesController::class, 'getOptionAttributesForCategory'],
    );

    Route::get(
        '/brands/{brandId}/categories',
        [CategoriesBrandController::class, 'getCategoriesForBrand'],
    )
        ->where('brandId', '[0-9a-zA-Z\-]+');
});
