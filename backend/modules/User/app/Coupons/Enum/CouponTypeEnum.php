<?php

namespace Modules\User\Coupons\Enum;

use InvalidArgumentException;

enum CouponTypeEnum: int
{
    case PERSONAL = 0;

    case GLOBAL = 1;

    public static function fromString(string $type): CouponTypeEnum
    {
        return match (true) {
            strtolower($type) === 'personal' => self::PERSONAL,
            strtolower($type) === 'global' => self::GLOBAL,
            default => throw new InvalidArgumentException('Invalid coupon type'),
        };
    }

    public function is(self $type): bool
    {
        return $this === $type;
    }

    public function toString(): string
    {
        return match ($this) {
            self::PERSONAL => 'personal',
            self::GLOBAL => 'global',
        };
    }
}
