<?php

declare(strict_types=1);

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpecAttributes;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\ProductAttribute;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Models\Warehouse;
use Ramsey\Uuid\Uuid;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'title_description' => fake()->paragraph(4),
            'main_description_markdown' => fake()->paragraph(20),
            'product_article' => fake()->ean13(),
            'preview_image' => url('storage/products/272_262_preview_product_preview.png'),
            'preview_image_source' => '272_262_preview_product_preview.png',
            'images' => [
                [
                    'order' => 1,
                    'source' => config('product.image.default'),
                    'id' => Uuid::uuid7()->toString(),
                    'image_url' => url('/storage/'.config('product.image.default')),
                ],
            ],
        ];
    }

    public function withSingleAttributes(): Product
    {
        $category = $this->fakeCategory();

        $productAttribute = ProductAttribute::factory()
            ->for($category)
            ->create();

        $product = $this
            ->state(['with_attribute_combinations' => false])
            ->for($category)
            ->for($this->fakeBrand())
            ->has(
                factory: ProductAttributeValue::factory()
                    ->count(2)
                    ->for($productAttribute, 'attribute'),
                relationship: 'singleAttributes',
            )
            ->hasAttached(
                factory: ProductSpecAttributes::factory()->count(2),
                pivot: ['value' => ['fake specification value']],
                relationship: 'productSpecs',
            )
            ->create();

        $this->addToWarehouse($product, false);

        return $product->load([
            'warehouse',
        ]);
    }

    public function withCombinedAttributes(): Product
    {
        $category = $this->fakeCategory();

        $attributes = ProductAttribute::factory()
            ->count(2)
            ->for($category)
            ->create();

        $product = $this
            ->state(['with_attribute_combinations' => true])
            ->for($category)
            ->for($this->fakeBrand())
            ->has(
                factory: ProductVariation::factory()
                    ->count(2)
                    ->hasAttached(
                        factory: $attributes,
                        pivot: ['value' => 'test-'.Str::random(5)],
                    ),
                relationship: 'combinedAttributes',
            )
            ->hasAttached(
                factory: ProductSpecAttributes::factory()->count(2),
                pivot: ['value' => ['fake specification value']],
                relationship: 'productSpecs',
            )
            ->create();

        $this->addToWarehouse($product, false);

        return $product->load([
            'warehouse',
        ]);
    }

    public function withoutAttributes(): Product
    {
        $product = $this
            ->for($this->fakeBrand())
            ->for($this->fakeCategory())
            ->hasAttached(
                factory: ProductSpecAttributes::factory()->count(2),
                pivot: ['value' => ['fake specification value']],
                relationship: 'productSpecs',
            )
            ->create();

        $this->addToWarehouse($product);

        return $product->load([
            'warehouse',
        ]);
    }

    public function published(): ProductFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProductStatusEnum::PUBLISHED->value,
            ];
        });
    }

    public function unPublished(): ProductFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProductStatusEnum::NOT_PUBLISHED->value,
            ];
        });
    }

    public function trashed(): ProductFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProductStatusEnum::TRASHED->value,
                'deleted_at' => now(),
            ];
        });
    }

    private function fakeCategory(): Category
    {
        return Category::query()->firstOrCreate([
            'name' => 'Seed Test Category',
        ]);
    }

    private function fakeBrand(): Brand
    {
        return Brand::query()->firstOrCreate([
            'name' => 'Seed Test Brand',
            'logo_url' => url('storage/brands/'.config('brand.default_logo')),
        ]);
    }

    private function addToWarehouse(Product $product, bool $price = true): void
    {
        $state = [
            'product_id' => $product->id,
            'status' => WarehouseProductStatusEnum::IN_STOCK->value,
        ];

        if (! $price) {
            $state['default_price'] = null;
        }

        Warehouse::factory()->state($state)->create();
    }
}
