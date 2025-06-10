<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Services;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;
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
            ->with(['users' => fn ($query) => $query->select('users.id', 'users.email')])
            ->allowedSorts(['expires_at'])
            ->where('coupons.type', CouponTypeEnum::PERSONAL)
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

    public function getPersonalForUser(User $user): LengthAwarePaginator
    {
        return Coupon::query()
            ->where('type', CouponTypeEnum::PERSONAL)
            ->whereHas('users', fn ($q) => $q->where('coupon_user.user_id', $user->id))
            ->paginate(config('user.retrieve.personal_coupons'));
    }

    public function getGlobalForUser(User $user): LengthAwarePaginator
    {
        return Coupon::query()
            ->select('coupons.*', DB::raw('COALESCE(coupon_user.usage_number, 0) as usage_number'))
            ->leftJoin('coupon_user', function ($join) use ($user) {
                $join
                    ->on('coupons.id', '=', 'coupon_user.coupon_id')
                    ->where('coupon_user.user_id', '=', $user->id);
            })
            ->where('type', CouponTypeEnum::GLOBAL)
            ->where(function ($query) {
                $query
                    ->whereNull('coupon_user.usage_number')
                    ->orWhere('coupon_user.usage_number', '<', config('user.coupons.usage_limit'));
            })
            ->paginate(config('user.retrieve.personal_coupons'));
    }
}
