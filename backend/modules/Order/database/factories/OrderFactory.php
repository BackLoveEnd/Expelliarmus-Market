<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_id' => randomNumber(12),
            'status' => OrderStatusEnum::PENDING->value,
            'created_at' => now(),
            'total_price' => 0,
        ];
    }

    public function user(?UserInterface $user = null, ?int $type = null): OrderFactory
    {
        return $this->state(function (array $attributes) use ($user, $type) {
            if (! $user) {
                $user = $type === 0 ? User::factory()->create() : Guest::factory()->create();

                return [
                    'userable_type' => $user::class,
                    'userable_id' => $user->id,
                    'contact_email' => $user->email,
                ];
            }

            return [
                'userable_type' => $user::class,
                'userable_id' => $user->id,
                'contact_email' => $user->email,
            ];
        });
    }

    public function totalPrice(float $price): OrderFactory
    {
        return $this->state(function (array $attributes) use ($price) {
            return [
                'total_price' => $price,
            ];
        });
    }
}
