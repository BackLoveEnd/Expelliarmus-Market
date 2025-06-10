<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Modules\User\Coupons\Http\Controllers\UserRetrieveCouponsController;
use Modules\User\Users\Http\Controllers\UserController;
use Modules\User\Users\Http\Controllers\UserWishlistController;

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::controller(UserWishlistController::class)->prefix('/wishlist')->group(function () {
        Route::get('/', 'getUserWishlist')->withoutMiddleware('throttle:api');

        Route::post('/products/{productBind}', 'addProductToWishList')
            ->whereNumber('productBind');

        Route::delete('/products/{productBind}', 'removeFromWishlist')
            ->whereNumber('productBind');

        Route::delete('/products', 'clearWishlist');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('user-profile-information.update');

    Route::put('/user/password', [PasswordController::class, 'update'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('user-password.update');
});

Route::prefix('shop/user')->middleware('auth:sanctum')->group(function () {
    Route::get('/coupons/personal', [UserRetrieveCouponsController::class, 'getMyPersonalCoupons'])
        ->withoutMiddleware('throttle:api');

    Route::get('/coupons/global', [UserRetrieveCouponsController::class, 'getMyGlobalCoupons'])
        ->withoutMiddleware('throttle:api');
});

Route::get('/current-user', [UserController::class, 'user']);
