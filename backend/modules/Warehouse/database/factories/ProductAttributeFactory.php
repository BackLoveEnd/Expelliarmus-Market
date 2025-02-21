<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Models\ProductAttribute;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition(): array
    {
        return [
            'name' => 'attr-'.fake()->unique()->ean8(),
            'type' => fake()->randomElement([
                ProductAttributeTypeEnum::NUMBER->value,
                ProductAttributeTypeEnum::STRING->value,
                ProductAttributeTypeEnum::COLOR->value,
            ]),
            'required' => false,
        ];
    }
}