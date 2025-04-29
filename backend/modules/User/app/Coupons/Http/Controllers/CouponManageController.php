<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Coupons\Dto\CouponDto;
use Modules\User\Coupons\Dto\CouponEditDto;
use Modules\User\Coupons\Exceptions\FailedToUpdateCouponException;
use Modules\User\Coupons\Http\Requests\CreateCouponRequest;
use Modules\User\Coupons\Http\Requests\EditCouponRequest;
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
     */
    public function create(CreateCouponRequest $request): CouponResource
    {
        $coupon = $this->couponManageService->createCoupon(
            dto: CouponDto::fromRequest($request),
        );

        return CouponResource::make($coupon);
    }

    /**
     * Update coupon data.
     *
     * Usage place - Admin section.
     *
     * @param  EditCouponRequest  $request
     * @param  Coupon  $coupon
     * @return JsonResponse
     * @throws FailedToUpdateCouponException
     */
    public function edit(EditCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $this->couponManageService->updateCoupon(
            coupon: $coupon,
            dto: CouponEditDto::fromRequest($request, $coupon),
        );

        return response()->json(['message' => 'Coupon updated successfully.']);
    }

    /**
     * Delete a coupon.
     *
     * Usage place - Admin section.
     *
     * @param  Coupon  $coupon
     * @return JsonResponse
     */
    public function delete(Coupon $coupon): JsonResponse
    {
        $this->couponManageService->deleteCoupon($coupon);

        return response()->json(['message' => 'Coupon deleted successfully.']);
    }
}