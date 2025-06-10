<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\Warehouse;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'total_quantity' => fake()->numberBetween(10, 1000),
            'default_price' => (float) fake()->numberBetween(10, 1000),
            'arrived_at' => now(),
            'status' => WarehouseProductStatusEnum::PENDING,
        ];
    }
}
