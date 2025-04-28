<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
            'user_id' => null,
            'email' => null,
            'discount' => $this->faker->numberBetween(10, 25),
            'expires_at' => now()->addYear(),
            'type' => CouponTypeEnum::GLOBAL->value,
        ];
    }

    public function user(User|string $user): CouponFactory
    {
        return $this->state(function (array $attributes) use ($user) {
            if ($user instanceof User) {
                return [
                    'user_id' => $user->id,
                ];
            }

            return [
                'email' => $user,
            ];
        });
    }
}