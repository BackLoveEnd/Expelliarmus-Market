<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Coupons\Http\Resources\CouponResource;
use Modules\User\Coupons\Http\Resources\GlobalCouponsForUserResource;
use Modules\User\Coupons\Services\CouponStorageService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserRetrieveCouponsController extends Controller
{
    public function __construct(
        private CouponStorageService $couponStorageService,
    ) {}

    /**
     * Retrieve personal coupons for user.
     *
     * Usage place - Shop.
     */
    public function getMyPersonalCoupons(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $coupons = $this->couponStorageService->getPersonalForUser($request->user('web'));

        if ($coupons->isEmpty()) {
            return response()->json(['message' => 'Personal coupons not found.'], 404);
        }

        return CouponResource::collection($coupons->getCollection())->additional([
            'meta' => [
                'total' => $coupons->total(),
            ],
            'links' => [
                'current' => $coupons->currentPage(),
                'next' => $coupons->hasMorePages() ? $coupons->currentPage() + 1 : null,
                'prev' => $coupons->currentPage() > 1 ? $coupons->currentPage() - 1 : null,
            ],
        ]);
    }

    /**
     * Retrieve global coupons for user.
     *
     * Usage place - Shop.
     */
    public function getMyGlobalCoupons(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $coupons = $this->couponStorageService->getGlobalForUser($request->user('web'));

        if ($coupons->isEmpty()) {
            return response()->json(['message' => 'Global coupons not found.'], 404);
        }

        return GlobalCouponsForUserResource::collection($coupons->getCollection())
            ->additional([
                'meta' => [
                    'total' => $coupons->total(),
                ],
                'links' => [
                    'current' => $coupons->currentPage(),
                    'next' => $coupons->hasMorePages() ? $coupons->currentPage() + 1 : null,
                    'prev' => $coupons->currentPage() > 1 ? $coupons->currentPage() - 1 : null,
                ],
            ]);
    }
}
