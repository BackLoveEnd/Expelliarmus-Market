<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Cart\Http\Controllers\CartController;

Route::prefix('shop/user/cart')->middleware('customer')->group(function () {
    Route::controller(CartController::class)->group(function () {
        Route::get('/', 'getClientCart')->withoutMiddleware('throttle:api');

        Route::post('/', 'addProductToCart');

        Route::patch('/', 'updateProductsQuantity');

        Route::delete('/', 'clearCart');

        Route::delete('/{id}', 'removeFromCart');
    });
});