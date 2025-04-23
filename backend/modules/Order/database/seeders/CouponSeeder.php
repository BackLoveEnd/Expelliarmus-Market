<?php

declare(strict_types=1);

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Order\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::factory()->count(5)->create();
    }
}