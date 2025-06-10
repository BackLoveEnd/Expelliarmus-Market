<?php

use Illuminate\Support\Facades\Route;
use Modules\Warehouse\Http\Controllers\DiscountController;
use Modules\Warehouse\Http\Controllers\RetrieveDiscountController;
use Modules\Warehouse\Http\Controllers\WarehouseController;
use Modules\Warehouse\Models\ProductAttribute;

Route::prefix('management')->group(function () {
    Route::get('/available-product-attributes', static function () {
        return ProductAttribute::query()->get(['id', 'name', 'type']);
    });

    Route::prefix('warehouse')->group(function () {
        Route::prefix('products')->middleware('auth.manager')->group(function () {
            Route::get('/', [WarehouseController::class, 'searchProductBySearchable'])
                ->withoutMiddleware(['throttle:api']);

            Route::get('/{productBind}', [WarehouseController::class, 'getWarehouseProductInfo'])
                ->whereNumber('productBind');

            Route::post('/{product}/discounts', [DiscountController::class, 'addDiscount'])
                ->whereNumber('product');

            Route::controller(DiscountController::class)->whereNumber(['product', 'discount'])->group(function () {
                Route::put('/{product}/discounts/{discount}', 'editDiscount');

                Route::delete('/{product}/discounts/{discount}', 'cancelDiscount');
            });

            Route::get('/discounted', [RetrieveDiscountController::class, 'getAllDiscountedProducts']);
        });

        Route::get('/table', [WarehouseController::class, 'getProductPaginated'])
            ->middleware('auth.manager')
            ->withoutMiddleware(['throttle:api']);
    });

    Route::prefix('discounts')->middleware('auth.manager')->group(function () {
        Route::controller(RetrieveDiscountController::class)->group(function () {
            Route::get('/products-available-for-discount', 'search');

            Route::get('/products/{productBind}', 'getProductWithDiscountsInfo')
                ->whereNumber('productBind');
        });
    });
});
