<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Order\Exceptions\CouponNotValidException;
use Modules\Order\Order\Http\Requests\CouponCheckRequest;
use Modules\Order\Order\Http\Resources\CouponResource;
use Modules\Order\Order\Services\CouponService;

class CouponController
{
    public function __construct(
        private CouponService $couponService,
    ) {}

    /**
     * Check if the coupon is valid.
     *
     * Usage place - Shop.
     *
     * @param  Request  $request
     * @return CouponResource|JsonResponse
     * @throws CouponNotValidException
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