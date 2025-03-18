<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Models\ProductAttributeValue;

class SingleAttributeFactory extends Factory
{

    protected $model = ProductAttributeValue::class;

    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(10, 1000),
            'price' => round(fake()->numberBetween(30, 100), 2),
            'value' => 'fake',
        ];
    }

}
