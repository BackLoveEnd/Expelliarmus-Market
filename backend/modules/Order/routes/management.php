<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Order\Http\Controllers\ManagementOrdersRetrieveService;

Route::prefix('management')->middleware('auth.manager')->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/pending', [ManagementOrdersRetrieveService::class, 'getPendingOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/cancelled', [ManagementOrdersRetrieveService::class, 'getCancelledOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/delivered', [ManagementOrdersRetrieveService::class, 'getDeliveredOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/refunded', [ManagementOrdersRetrieveService::class, 'getRefundedOrders'])
            ->withoutMiddleware('throttle:api');

        Route::get('/{model:order_id}/lines', [ManagementOrdersRetrieveService::class, 'getOrderLines'])
            ->withoutMiddleware('throttle:api');
    });
});