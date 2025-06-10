<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Mockery;
use Modules\Category\Database\Seeders\CategoryDatabaseSeeder;
use Modules\Category\Models\Category;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\Product\Http\Management\Actions\Product\Edit\DeleteVariationsWhenNeedAction;
use Modules\Product\Http\Management\Actions\Product\Edit\EditProduct;
use Modules\Product\Http\Management\Actions\Product\Edit\EditProductFactoryAction;
use Modules\Product\Http\Management\Requests\ProductEditRequest;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpec;
use Modules\Product\Models\ProductSpecAttributes;
use Modules\Warehouse\Database\Seeders\WarehouseDatabaseSeeder;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;
use Modules\Warehouse\Models\ProductAttribute;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Models\VariationAttributeValues;

class ProductEditTest extends TestCase
{
    use RefreshDatabase;

    private const int ALL_NEW_ATTRS = 0;

    private const int ALL_EXISTS_ATTRS = 1;

    private const int BALANCE_ATTRS = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            ProductDatabaseSeeder::class,
            CategoryDatabaseSeeder::class,
            WarehouseDatabaseSeeder::class,
        ]);
    }

    public function test_can_edit_product_specifications(): void
    {
        $product = Product::factory()->withoutAttributes();

        $specs = $this->generateSpecs($product->category);

        $request = $this->createRequestFromExists($product, [
            'product_specs' => $specs,
        ]);

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        // Add new specification attribute
        $this->assertTrue(
            ProductSpec::query()
                ->where('product_id', $product->id)
                ->where('attribute_id', $specs[0]['specifications'][0]['id'])
                ->whereJsonContains('value', $specs[0]['specifications'][0]['value'])
                ->exists()
        );

        $specAttributes = ProductSpecAttributes::query()
            ->where('spec_name', $specs[1]['specifications'][0]['spec_name'])
            ->first(['id']);

        // Change exist specifications
        $this->assertTrue(
            ProductSpec::query()
                ->where('product_id', $product->id)
                ->where('attribute_id', $specAttributes->id)
                ->whereJsonContains('value', $specs[1]['specifications'][0]['value'])
                ->exists()
        );
    }

    public function test_can_edit_product_without_options(): void
    {
        $product = Product::factory()->withoutAttributes();

        $specs = $this->generateSpecs($product->category);

        $request = $this->createRequestFromExists($product, [
            'title' => 'New Title',
            'product_specs' => $specs,
        ]);

        // Before changes
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => $product->title,
        ]);

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        // After changes
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'New Title',
        ]);
    }

    public function test_can_edit_product_with_single_option_with_new_attribute(): void
    {
        $product = Product::factory()->withSingleAttributes();

        $oldAttributeId = $product->singleAttributes->first()->attribute_id;

        $option = $this->generateSingleOption($product, true);

        $request = $this->createRequestFromExists($product, [
            'is_combined_attributes' => false,
            'single_variations' => $option,
            'product_specs' => $this->generateSpecs($product->category),
        ]);

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'with_attribute_combinations' => false,
        ]);

        // Old attribute data removed.
        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $oldAttributeId,
        ]);

        // New attribute data present.
        foreach ($option[0]['attributes'] as $attribute) {
            $this->assertDatabaseHas('product_attribute_values', [
                'product_id' => $product->id,
                'attribute_id' => $option[0]['attribute_id'],
                'quantity' => $attribute['quantity'],
                'price' => $attribute['price'],
                'value' => $attribute['value'],
            ]);
        }
    }

    public function test_can_edit_product_with_single_option_without_attribute_change(): void
    {
        $product = Product::factory()->withSingleAttributes();

        $option = $this->generateSingleOption($product, true, true);

        $request = $this->createRequestFromExists($product, [
            'is_combined_attributes' => false,
            'single_variations' => $option,
            'product_specs' => $this->generateSpecs($product->category),
        ]);

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'with_attribute_combinations' => false,
        ]);

        // Exists attributes was edited without attribute change.
        foreach ($option[0]['attributes'] as $attribute) {
            $this->assertDatabaseHas('product_attribute_values', [
                'product_id' => $product->id,
                'attribute_id' => $product->singleAttributes->first()->attribute_id,
                'quantity' => $attribute['quantity'],
                'price' => $attribute['price'],
                'value' => $attribute['value'],
            ]);
        }
    }

    public function test_can_edit_product_with_combined_options_with_new_attributes_and_variation(): void
    {
        $product = Product::factory()->withCombinedAttributes();

        $options = $this->generateCombinedOptionWithNewAllAttrs();

        $request = $this->createRequestFromExists($product, [
            'is_combined_attributes' => true,
            'product_specs' => $this->generateSpecs($product->category),
            'combined_variations' => $options,
        ]);

        $attributeNames = collect($options[0]['attributes'])->pluck('name');

        $oldSVariationIds = $product->combinedAttributes->pluck('id');

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'with_attribute_combinations' => true,
        ]);

        // New variation created, so old ones must be removed.
        foreach ($oldSVariationIds as $id) {
            $this->assertDatabaseMissing('product_variations', [
                'id' => $id,
            ]);
        }

        // New variations data
        foreach ($options as $option) {
            $this->assertDatabaseHas('product_variations', [
                'product_id' => $product->id,
                'sku' => $option['sku'],
                'price' => $option['price'],
                'quantity' => $option['quantity'],
            ]);
        }

        $variations = ProductVariation::query()
            ->where('product_id', $product->id)
            ->get(['id']);

        $attributes = ProductAttribute::query()
            ->whereIn('name', $attributeNames)
            ->get(['id']);

        // New variation related to attribute inside it.
        foreach ($variations as $varKey => $variation) {
            foreach ($attributes as $attrKey => $attribute) {
                $this->assertTrue(
                    VariationAttributeValues::query()
                        ->where('variation_id', $variation->id)
                        ->where('attribute_id', $attribute->id)
                        ->where('value', $options[$varKey]['attributes'][$attrKey]['value'])
                        ->exists()
                );
            }
        }
    }

    public function test_can_edit_product_with_combined_options_with_new_attributes_and_exist_variations(
    ): void {
        $product = Product::factory()->withCombinedAttributes();

        $options = $this->generateCombinedOptionsWithExistsVariationsAndNewAttrs($product);

        $request = $this->createRequestFromExists($product, [
            'is_combined_attributes' => true,
            'product_specs' => $this->generateSpecs($product->category),
            'combined_variations' => $options,
        ]);

        $attributeNames = collect($options->first()['attributes'])->pluck('name');

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        $variations = $options->pluck('id');

        // Only if variations changed (by new SKU)
        /*$variations = ProductVariation::query()
            ->where('product_id', $product->id)
            ->get(['id'])
            ->pluck('id');*/

        $attributes = ProductAttribute::query()
            ->whereIn('name', $attributeNames)
            ->get(['id']);

        // Check for new variations and relation between them and attributes
        foreach ($variations as $varKey => $variation) {
            $this->assertDatabaseHas('product_variations', [
                'product_id' => $product->id,
                'sku' => $options[$varKey]['sku'],
                'price' => $options[$varKey]['price'],
                'quantity' => $options[$varKey]['quantity'],
            ]);

            foreach ($attributes as $attrKey => $attribute) {
                $this->assertTrue(
                    VariationAttributeValues::query()
                        ->where('variation_id', $variation)
                        ->where('attribute_id', $attribute->id)
                        ->where('value', $options[$varKey]['attributes'][$attrKey]['value'])
                        ->exists()
                );
            }
        }
    }

    // Balanced means some variations or attributes exists, some new
    public function test_can_edit_product_with_combined_options_with_balanced_variations_and_balanced_attributes(
    ): void {
        $product = Product::factory()->withCombinedAttributes();

        $options = $this->generateCombinedOptionsWithBalanceAttrsAndVariations($product);

        $request = $this->createRequestFromExists($product, [
            'product_specs' => $this->generateSpecs($product->category),
            'is_combined_attributes' => true,
            'combined_variations' => $options,
        ]);

        $attributeNames = collect($options[0]['attributes'])->pluck('name');

        (new EditProductFactoryAction)->createAction($request)
            ->handle(
                new EditProduct($product, new DeleteVariationsWhenNeedAction),
                new EditProductInWarehouse($product)
            );

        $variations = ProductVariation::query()
            ->where('product_id', $product->id)
            ->get(['id'])
            ->pluck('id');

        // Check for new variations
        foreach ($options as $option) {
            $this->assertDatabaseHas('product_variations', [
                'sku' => $option['sku'],
                'price' => $option['price'],
                'quantity' => $option['quantity'],
                'product_id' => $product->id,
            ]);
        }

        $attributes = ProductAttribute::query()
            ->whereIn('name', $attributeNames)
            ->get(['id']);

        // Check for relations between newly created and exists variation and attributes
        foreach ($variations as $varKey => $variation) {
            foreach ($attributes as $attrKey => $attribute) {
                $this->assertTrue(
                    VariationAttributeValues::query()
                        ->where('variation_id', $variation)
                        ->where('attribute_id', $attribute->id)
                        ->where('value', $options[$varKey]['attributes'][$attrKey]['value'])
                        ->exists()
                );
            }
        }
    }

    public function test_delete_single_option_when_edit_from_single_to_combined_options(): void
    {
        $product = Product::factory()->withSingleAttributes();

        // true - means combined
        (new DeleteVariationsWhenNeedAction)->handle($product, true);

        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $product->id,
        ]);
    }

    public function test_delete_combined_options_when_edit_from_combined_to_single_option(): void
    {
        $product = Product::factory()->withCombinedAttributes();

        $variations = $product->combinedAttributes->pluck('id');

        // false - means single option
        (new DeleteVariationsWhenNeedAction)->handle($product, false);

        $this->assertDatabaseMissing('product_variations', [
            'product_id' => $product->id,
        ]);

        foreach ($variations as $variation) {
            $this->assertDatabaseMissing('variation_attribute_values', [
                'variation_id' => $variation,
            ]);
        }
    }

    public function test_delete_all_options_when_edit_from_combined_or_single_to_without_option(): void
    {
        $productWithSingle = Product::factory()->withSingleAttributes();

        $productWithCombined = Product::factory()->withCombinedAttributes();

        $variations = $productWithCombined->combinedAttributes->pluck('id');

        // null - means without options
        (new DeleteVariationsWhenNeedAction)->handle($productWithSingle, null);

        (new DeleteVariationsWhenNeedAction)->handle($productWithCombined, null);

        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $productWithSingle->id,
        ]);

        $this->assertDatabaseMissing('product_variations', [
            'product_id' => $productWithCombined->id,
        ]);

        foreach ($variations as $variation) {
            $this->assertDatabaseMissing('variation_attribute_values', [
                'variation_id' => $variation,
            ]);
        }
    }

    private function generateCombinedOptionWithNewAllAttrs(): array
    {
        return [
            [
                'sku' => uniqid('', true),
                'quantity' => random_int(100, 1000),
                'price' => random_int(100, 1000),
                'attributes' => [
                    [
                        'name' => 'Test New Attr 3',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 1',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                    [
                        'name' => 'Test New Combined Attr 4',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 2',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                ],
            ],
            [
                'sku' => uniqid('', true),
                'quantity' => random_int(100, 1000),
                'price' => random_int(100, 1000),
                'attributes' => [
                    [
                        'name' => 'Test New Attr 3',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 3',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                    [
                        'name' => 'Test New Combined Attr 4',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 4',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                ],
            ],
        ];
    }

    private function generateCombinedOptionsWithExistsVariationsAndNewAttrs(Product $product)
    {
        return $product->combinedAttributes->map(function (ProductVariation $variation) {
            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => round(512, 2),
                'quantity' => $variation->quantity,
                'attributes' => [
                    [
                        'name' => 'Test New Attr 51',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 15',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                    [
                        'name' => 'Test New Attr 12',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 15',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                ],
            ];
        });
    }

    private function generateCombinedOptionsWithBalanceAttrsAndVariations(Product $product): array
    {
        $attribute = ProductAttribute::factory()->category($product->category)
            ->create();

        $variation = $product->combinedAttributes->first();

        return [
            [
                'sku' => $variation->sku,
                'quantity' => 500,
                'price' => random_int(100, 1000),
                'attributes' => [
                    [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'type' => [
                            'id' => $attribute->type->value,
                            'name' => $attribute->type->toTypes(),
                        ],
                        'value' => 'test value 1',
                        'attribute_view_type' => $attribute->view_type->value,
                    ],
                    [
                        'name' => 'New Combined Attribute',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 10',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                ],
            ],
            [
                'sku' => uniqid('', true),
                'quantity' => 20,
                'price' => random_int(100, 1000),
                'attributes' => [
                    [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'type' => [
                            'id' => $attribute->type->value,
                            'name' => $attribute->type->toTypes(),
                        ],
                        'value' => 'test value 3',
                        'attribute_view_type' => $attribute->view_type->value,
                    ],
                    [
                        'name' => 'New Combined Attribute',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                            'name' => ProductAttributeTypeEnum::COLOR->name,
                        ],
                        'value' => 'test value 10',
                        'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    ],
                ],
            ],
        ];
    }

    private function generateSingleOption(
        Product $product,
        bool $withExistAttribute = false,
        bool $overrideExistsOptions = false
    ): array {
        if ($overrideExistsOptions) {
            return [
                [
                    'attributes' => [
                        [
                            'quantity' => 50,
                            'price' => 100,
                            'value' => 'Test Single Value',
                        ],
                        [
                            'quantity' => 50,
                            'price' => 200,
                            'value' => 'Another Single Value',
                        ],
                    ],
                    'attribute_id' => $product->singleAttributes->first()->attribute_id,
                ],
            ];
        }

        $result = [
            'attributes' => [
                [
                    'quantity' => 50,
                    'price' => 100,
                    'value' => 'Test Single Value',
                ],
                [
                    'quantity' => 50,
                    'price' => 200,
                    'value' => 'Another Single Value',
                ],
            ],
        ];

        $attributes = [
            'attribute_name' => 'Test Single Attribute',
            'attribute_type' => ProductAttributeTypeEnum::STRING->value,
            'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
        ];

        if ($withExistAttribute) {
            $attributes['attribute_id'] = ProductAttribute::query()->create([
                'name' => $attributes['attribute_name'],
                'type' => $attributes['attribute_type'],
                'view_type' => $attributes['attribute_view_type'],
                'category_id' => $product->category->id,
            ])->id;
        }

        return [array_merge($result, $attributes)];
    }

    private function generateSpecs(Category $category): array
    {
        return [
            [
                'group' => 'Example',
                'specifications' => [
                    [
                        'id' => ProductSpecAttributes::query()->create([
                            'spec_name' => 'Example spec',
                            'group_name' => 'Example',
                            'category_id' => $category->id,
                        ])->id,
                        'spec_name' => 'Example spec',
                        'value' => ['test value'],
                    ],
                ],
            ],
            [
                'group' => null,
                'specifications' => [
                    [
                        'id' => null,
                        'spec_name' => 'Without Group Spec',
                        'value' => ['test value'],
                    ],
                ],
            ],
        ];
    }

    private function createRequestFromExists(Product $product, array $overrides)
    {
        $request = Mockery::mock(ProductEditRequest::class);

        $request->shouldReceive('relation')
            ->with('product_variations_combinations')
            ->andReturn(collect($overrides['combined_variations'] ?? []));

        $request->shouldReceive('relation')
            ->with('product_variation')
            ->andReturn(collect($overrides['single_variations'] ?? []));

        $request->shouldReceive('relation')
            ->with('category')
            ->andReturn(['id' => $overrides['category']['id'] ?? $product->category_id]);

        $request->shouldReceive('relation')
            ->with('brand')
            ->andReturn(['id' => $overrides['brand']['id'] ?? $product->brand_id]);

        $request->shouldReceive('relation')
            ->with('product_specs')
            ->andReturn(
                collect($overrides['product_specs'])
            );

        $request->is_combined_attributes = $overrides['is_combined_attributes'] ?? $product->with_attribute_combinations;
        $request->title = $overrides['title'] ?? $product->title;
        $request->title_description = $overrides['title_description'] ?? $product->title_description;
        $request->main_description = $overrides['main_description'] ?? $product->main_description_markdown;
        $request->total_quantity = $overrides['total_quantity'] ?? $product->warehouse->total_quantity;
        $request->price = $overrides['price'] ?? $product->warehouse->default_price;
        $request->product_article = $overrides['product_article'] ?? $product->product_article;

        return $request;
    }
}
