<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Order\Models\Order;

class OrderFactory extends Factory
{

    protected $model = Order::class;

    public function definition(): array
    {
        return [];
    }
}