<?php

namespace Modules\Order\Order\Enum;

enum CouponTypeEnum: int
{
    case PERSONAL = 0;

    case GLOBAL = 1;

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
