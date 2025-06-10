<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->truncate();

        $users = User::factory(10)->create();

        $users->each(function (User $user) {
            $user->assignRole('regular_user');
        });

        $users->take(3)->each(function (User $user) {
            Coupon::factory()->user($user)->type(CouponTypeEnum::PERSONAL)->create();
        });
    }
}
