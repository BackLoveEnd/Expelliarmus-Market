<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Cart\Http\Controllers\CartController;
use Modules\Order\Order\Http\Controllers\CouponController;
use Modules\Order\Order\Http\Controllers\OrderCreateController;

Route::prefix('shop/user/cart')->middleware('customer')->group(function () {
    Route::controller(CartController::class)->group(function () {
        Route::get('/', 'getClientCart')->withoutMiddleware('throttle:api');

        Route::post('/', 'addProductToCart');

        Route::patch('/', 'updateProductsQuantity');

        Route::delete('/', 'clearCart');

        Route::delete('/{id}', 'removeFromCart');
    });
});

Route::prefix('shop/user/order')->middleware('customer')->group(function () {
    Route::post('/checkout', OrderCreateController::class);
});

Route::prefix('shop/users/coupons')->middleware('customer')->group(function () {
    Route::get('/{coupon}/check', [CouponController::class, 'checkCoupon']);
});