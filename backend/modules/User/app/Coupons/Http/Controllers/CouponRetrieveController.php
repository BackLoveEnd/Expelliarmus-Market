<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Modules\User\Coupons\Http\Resources\CouponResource;
use Modules\User\Coupons\Http\Resources\PersonalCouponsResource;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Coupons\Services\CouponStorageService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CouponRetrieveController extends Controller
{
    public function __construct(
        private CouponStorageService $storageService,
    ) {}

    /**
     * Get global coupons.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getGlobalCoupons(Request $request): JsonApiResourceCollection
    {
        $this->authorize('view', Coupon::class);

        $dto = $this->storageService->getGlobals(
            limit: (int) $request->query('limit', config('user.retrieve.coupons')),
            offset: (int) $request->query('offset', 0),
        );

        return CouponResource::collection($dto->items)->additional($dto->wrapMeta());
    }

    /**
     * Get personal coupons.
     *
     * Usage place - User section.
     *
     * @throws AuthorizationException
     */
    public function getPersonalCoupons(Request $request): JsonApiResourceCollection
    {
        $this->authorize('view', Coupon::class);

        $dto = $this->storageService->getPersonal(
            limit: (int) $request->query('limit', config('user.retrieve.coupons')),
            offset: (int) $request->query('offset', 0),
        );

        return PersonalCouponsResource::collection($dto->items)->additional($dto->wrapMeta());
    }
}
