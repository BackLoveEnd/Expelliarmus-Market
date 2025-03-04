<?php

use Illuminate\Support\Facades\Route;
use Modules\Warehouse\Http\Controllers\RetrieveDiscountController;
use Modules\Warehouse\Http\Controllers\WarehouseController;
use Modules\Warehouse\Models\ProductAttribute;

Route::prefix('management')->group(function () {
    Route::get('/available-product-attributes', function () {
        return ProductAttribute::query()->get(['id', 'name', 'type']);
    });

    Route::prefix('warehouse')->group(function () {
        Route::get('/products', [WarehouseController::class, 'searchProductBySearchable'])
            ->withoutMiddleware(['throttle']);

        Route::get('/table', [WarehouseController::class, 'getProductPaginated'])
            ->withoutMiddleware(['throttle']);

        Route::get('/products/{productBind}', [WarehouseController::class, 'getWarehouseProductInfo'])
            ->whereNumber('productBind');
    });

    Route::prefix('discounts')->group(function () {
        Route::controller(RetrieveDiscountController::class)->group(function () {
            Route::get('/products-available-for-discount', 'search');

            Route::get('/products/{productBind}', 'getProductWithDiscountsInfo')
                ->whereNumber('productBind');
        });
    });
});