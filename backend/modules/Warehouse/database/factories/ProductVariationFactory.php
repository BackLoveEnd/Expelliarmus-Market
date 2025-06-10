<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Models\ProductVariation;

class ProductVariationFactory extends Factory
{
    protected $model = ProductVariation::class;

    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(10, 1000),
            'price' => (float) number_format(fake()->numberBetween(30, 100)),
            'sku' => fake()->unique()->ean8(),
        ];
    }
}
