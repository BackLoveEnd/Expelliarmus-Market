<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Dto;

use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;

final readonly class CouponUserDto
{
    public function __construct(
        public Coupon $coupon,
        public User|string $user,
    ) {}
}
