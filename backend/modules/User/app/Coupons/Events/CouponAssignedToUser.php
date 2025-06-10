<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Events;

use Modules\User\Coupons\Models\Coupon;

final readonly class CouponAssignedToUser
{
    public function __construct(
        public string $email,
        public Coupon $coupon,
    ) {}
}
