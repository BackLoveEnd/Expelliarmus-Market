<?php

declare(strict_types=1);

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\User;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'coupon_id' => strtoupper($this->faker->unique()->word()),
            'discount' => $this->faker->numberBetween(10, 25),
            'expires_at' => now()->addYear(),
            'type' => CouponTypeEnum::GLOBAL->value,
        ];
    }

    public function user(User|string $user): CouponFactory
    {
        return $this->afterCreating(function (Coupon $coupon) use ($user) {
            DB::table('coupon_user')->insert([
                'user_id' => $user instanceof User ? $user->id : null,
                'coupon_id' => $coupon->id,
                'usage_number' => 0,
                'email' => is_string($user) ? $user : null,
            ]);
        });
    }

    public function type(CouponTypeEnum $type): CouponFactory
    {
        return $this->state(function ($attributes) use ($type) {
            return [
                'type' => $type->value,
            ];
        });
    }
}
