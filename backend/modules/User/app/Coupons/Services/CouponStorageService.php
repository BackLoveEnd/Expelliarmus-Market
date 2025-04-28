<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Services;

use App\Services\Pagination\LimitOffsetDto;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Spatie\QueryBuilder\QueryBuilder;

class CouponStorageService
{
    public function getGlobals(int $limit, int $offset): LimitOffsetDto
    {
        $coupons = QueryBuilder::for(Coupon::class)
            ->allowedSorts(['expires_at'])
            ->where('type', CouponTypeEnum::GLOBAL)
            ->limit($limit)
            ->offset($offset)
            ->get([
                'id',
                'coupon_id',
                'type',
                'discount',
                'expires_at',
            ]);

        return new LimitOffsetDto(
            items: $coupons,
            total: Coupon::query()->where('type', CouponTypeEnum::GLOBAL)->count(),
            limit: $limit,
            offset: $offset,
        );
    }

    public function getPersonal(int $limit, int $offset): LimitOffsetDto
    {
        $coupons = QueryBuilder::for(Coupon::class)
            ->join('users', 'coupons.user_id', '=', 'users.id')
            ->allowedSorts(['expires_at'])
            ->where('type', CouponTypeEnum::PERSONAL)
            ->limit($limit)
            ->offset($offset)
            ->get();

        return new LimitOffsetDto(
            items: $coupons,
            total: Coupon::query()->where('type', CouponTypeEnum::PERSONAL)->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}