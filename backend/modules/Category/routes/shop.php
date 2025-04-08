<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Shop\Controllers\CategoryOptionAttributesController;
use Modules\Category\Http\Shop\Controllers\ShopCategoryAccessController;

Route::prefix('shop')->withoutMiddleware(['throttle'])->group(function () {
    Route::get('/home/categories/browse', [ShopCategoryAccessController::class, 'getCategoriesBrowseList']);

    Route::get('/categories/{category:slug}/children', [ShopCategoryAccessController::class, 'getChildrenOfCategory']);

    Route::get(
        '/categories/{categorySlug}/attributes',
        [CategoryOptionAttributesController::class, 'getOptionAttributesForCategory'],
    );
});