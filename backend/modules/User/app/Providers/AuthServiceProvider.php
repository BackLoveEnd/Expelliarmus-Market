<?php

declare(strict_types=1);

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as LaraAuthProvider;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Coupons\Policies\CouponPolicy;

class AuthServiceProvider extends LaraAuthProvider
{
    protected $policies = [
        Coupon::class => CouponPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
