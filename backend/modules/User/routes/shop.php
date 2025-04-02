<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserWishlistController;

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::controller(UserWishlistController::class)->prefix('/wishlist')->group(function () {
        Route::get('/', 'getUserWishlist')->withoutMiddleware('throttle');

        Route::post('/products/{productBind}', 'addProductToWishList')
            ->whereNumber('productBind');

        Route::delete('/products/{productBind}', 'removeFromWishlist')
            ->whereNumber('productBind');

        Route::delete('/products', 'clearWishlist');
    });
});