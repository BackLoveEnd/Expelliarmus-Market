<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;
use Modules\Warehouse\Models\ProductAttribute;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition(): array
    {
        return [
            'name' => 'attr-'.fake()->unique()->ean8(),
            'type' => ProductAttributeTypeEnum::STRING->value,
            'view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
            'required' => false,
        ];
    }

    public function category(Category $category): ProductAttributeFactory
    {
        return $this->state([
            'category_id' => $category->id,
        ]);
    }
}
