<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Coupons\Exceptions\CouponNotValidException;
use Modules\User\Coupons\Exceptions\ReachedGlobalCouponUserLimitException;
use Modules\User\Coupons\Http\Resources\CouponResource;
use Modules\User\Coupons\Services\CouponManageService;

class CouponCheckController
{
    public function __construct(
        private CouponManageService $couponService,
    ) {}

    /**
     * Check if the coupon is valid.
     *
     * Usage place - Shop.
     *
     * @throws CouponNotValidException|ReachedGlobalCouponUserLimitException
     */
    public function checkCoupon(Request $request): CouponResource|JsonResponse
    {
        $couponCode = $request->route()?->parameter('coupon');

        if ($couponCode === null) {
            return response()->json(['message' => 'Coupon code is required'], 422);
        }

        $coupon = $this->couponService->checkCoupon(
            couponCode: $couponCode,
            user: $request->user('web'),
        );

        return CouponResource::make($coupon);
    }
}
