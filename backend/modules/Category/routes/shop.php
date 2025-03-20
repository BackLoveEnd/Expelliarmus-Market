<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Shop\Controllers\ShopCategoryAccessController;

Route::prefix('shop')->group(function () {
    Route::get('/home/categories/browse', [ShopCategoryAccessController::class, 'getCategoriesBrowseList']);
});