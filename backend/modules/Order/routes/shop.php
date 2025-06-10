<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Cart\Http\Controllers\CartController;
use Modules\Order\Order\Http\Controllers\OrderCancelController;
use Modules\Order\Order\Http\Controllers\OrderCreateController;
use Modules\Order\Order\Http\Controllers\UserOrdersRetrieveController;
use Modules\User\Coupons\Http\Controllers\CouponCheckController;

Route::prefix('shop/user/cart')->middleware('customer')->group(function () {
    Route::controller(CartController::class)->group(function () {
        Route::get('/', 'getClientCart')->withoutMiddleware('throttle:api');

        Route::post('/', 'addProductToCart');

        Route::patch('/', 'updateProductsQuantity');

        Route::delete('/', 'clearCart');

        Route::delete('/{id}', 'removeFromCart');
    });
});

Route::prefix('shop/user/orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserOrdersRetrieveController::class, 'getOrderHistory'])
        ->withoutMiddleware('throttle:api');

    Route::get('/cancelled', [UserOrdersRetrieveController::class, 'getCancelledOrders'])
        ->withoutMiddleware('throttle:api');

    Route::delete('/{order:order_id}', OrderCancelController::class);
});

Route::prefix('shop/user/orders')->middleware('customer')->group(function () {
    Route::post('/checkout', OrderCreateController::class);
});

Route::prefix('shop/users/coupons')->middleware('customer')->group(function () {
    Route::get('/{coupon}/check', [CouponCheckController::class, 'checkCoupon']);
});
