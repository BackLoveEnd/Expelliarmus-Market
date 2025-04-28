<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\User\Coupons\Dto\CouponDto;
use Modules\User\Coupons\Http\Requests\CreateCouponRequest;
use Modules\User\Coupons\Http\Resources\CouponResource;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Coupons\Services\CouponManageService;

class CouponManageController extends Controller
{
    public function __construct(
        private CouponManageService $couponManageService,
    ) {}

    /**
     * Create a new coupon.
     *
     * Usage place - Admin section.
     *
     * @param  CreateCouponRequest  $request
     * @return CouponResource
     * @throws AuthorizationException
     */
    public function create(CreateCouponRequest $request): CouponResource
    {
        $this->authorize('manage', Coupon::class);

        $coupon = $this->couponManageService->createCoupon(
            dto: CouponDto::fromRequest($request),
        );

        return CouponResource::make($coupon);
    }
}