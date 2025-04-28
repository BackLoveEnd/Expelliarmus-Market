<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\Coupon;

use Modules\Order\Order\Enum\CouponTypeEnum;
use Modules\Order\Order\Exceptions\CouponNotValidException;
use Modules\Order\Order\Models\Coupon;
use Modules\User\Users\Models\User;

class CouponService
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

    public function deleteCoupon(Coupon $coupon): void
    {
        if ($coupon->type->is(CouponTypeEnum::PERSONAL)) {
            $coupon->delete();
        }
    }
}