<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Order\Http\Controllers\ManagementOrdersRetrieveController;

Route::prefix('management')->middleware('auth.manager')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/pending', [ManagementOrdersRetrieveController::class, 'getPendingOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/cancelled', [ManagementOrdersRetrieveController::class, 'getCancelledOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/delivered', [ManagementOrdersRetrieveController::class, 'getDeliveredOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/refunded', [ManagementOrdersRetrieveController::class, 'getRefundedOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/{model:order_id}/lines', [ManagementOrdersRetrieveController::class, 'getOrderLines'])
            ->withoutMiddleware('throttle:api');
    });
});
