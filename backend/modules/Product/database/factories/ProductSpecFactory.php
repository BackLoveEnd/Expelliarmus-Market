<?php

declare(strict_types=1);

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Models\ProductSpecAttributes;

class ProductSpecFactory extends Factory
{
    protected $model = ProductSpecAttributes::class;

    public function definition(): array
    {
        return [
            'spec_name' => 'specification-'.fake()->ean8(),
        ];
    }
}
