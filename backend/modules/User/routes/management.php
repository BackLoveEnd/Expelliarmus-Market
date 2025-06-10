<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Coupons\Http\Controllers\CouponManageController;
use Modules\User\Coupons\Http\Controllers\CouponRetrieveController;
use Modules\User\Users\Http\Controllers\GuestsController;
use Modules\User\Users\Http\Controllers\UserController;

Route::prefix('management/users')->middleware('auth.manager')->group(function () {
    Route::get('/regular-customers', [UserController::class, 'getRegularCustomers'])
        ->withoutMiddleware(['throttle:api']);

    Route::prefix('/coupons')->group(function () {
        Route::get('/global', [CouponRetrieveController::class, 'getGlobalCoupons'])
            ->withoutMiddleware(['throttle:api']);

        Route::get('/personal', [CouponRetrieveController::class, 'getPersonalCoupons'])
            ->withoutMiddleware(['throttle:api']);

        Route::post('/', [CouponManageController::class, 'create']);

        Route::put('/{coupon:coupon_id}', [CouponManageController::class, 'edit']);

        Route::delete('/{coupon:coupon_id}', [CouponManageController::class, 'delete'])
            ->withoutMiddleware(['throttle:api']);
    });

    Route::get('/guests', [GuestsController::class, 'getGuests'])
        ->withoutMiddleware(['throttle:api']);
});
