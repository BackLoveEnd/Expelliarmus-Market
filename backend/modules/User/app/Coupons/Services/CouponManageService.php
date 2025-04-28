<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Services;

use Illuminate\Support\Facades\DB;
use Modules\User\Coupons\Dto\CouponDto;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Exceptions\CouponNotValidException;
use Modules\User\Coupons\Exceptions\PersonalCouponMustHaveUserException;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;

class CouponManageService
{
    public function checkCoupon(string $couponCode, User|string|null $user): Coupon
    {
        $coupon = Coupon::query()
            ->where('coupon_id', $couponCode)
            ->where('expires_at', '>=', now())
            ->first();

        if (! $coupon) {
            throw new CouponNotValidException();
        }

        if ($coupon->type->is(CouponTypeEnum::GLOBAL)) {
            return $coupon;
        }

        if ($user instanceof User && $coupon->user_id === $user->id) {
            return $coupon;
        }

        if ($coupon->email && $coupon->email === $user) {
            return $coupon;
        }

        throw new CouponNotValidException();
    }

    public function createCoupon(CouponDto $dto): Coupon
    {
        return DB::transaction(function () use ($dto) {
            if ($dto->type->is(CouponTypeEnum::GLOBAL)) {
                return $this->saveGlobalCoupon($dto);
            }

            if (! $dto->email && $dto->type->is(CouponTypeEnum::PERSONAL)) {
                throw new PersonalCouponMustHaveUserException();
            }

            return $this->savePersonalCoupon($dto);
        });
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        if ($coupon->type->is(CouponTypeEnum::PERSONAL)) {
            $coupon->delete();
        }
    }

    protected function saveGlobalCoupon(CouponDto $dto): Coupon
    {
        return Coupon::query()->create([
            'coupon_id' => $dto->couponCode ?? randomString(12, true),
            'discount' => $dto->discount,
            'type' => $dto->type->value,
            'expires_at' => $dto->expiresAt,
            'user_id' => null,
            'email' => null,
        ]);
    }

    protected function savePersonalCoupon(CouponDto $dto): Coupon
    {
        $user = User::query()->where('email', $dto->email)->first(['id', 'email']);

        $coupon = Coupon::query()->create([
            'coupon_id' => $dto->couponCode ?? randomString(12, true),
            'discount' => $dto->discount,
            'type' => $dto->type->value,
            'expires_at' => $dto->expiresAt,
            'user_id' => $user?->id,
            'email' => $user ? null : $dto->email,
        ]);

        if ($user) {
            $coupon->setRelation('user', $user);
        }

        return $coupon;
    }
}