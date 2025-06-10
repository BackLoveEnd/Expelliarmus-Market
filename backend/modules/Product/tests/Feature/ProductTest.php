<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Mockery;
use Modules\Brand\Models\Brand;
use Modules\Category\Database\Seeders\CategoryDatabaseSeeder;
use Modules\Category\Models\Category;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\Product\Http\Management\Actions\Product\Create\CreateProduct;
use Modules\Product\Http\Management\Actions\Product\Create\CreateProductFactoryAction;
use Modules\Product\Http\Management\DTO\Images\MainImageDto;
use Modules\Product\Http\Management\DTO\Images\ProductImageDto;
use Modules\Product\Http\Management\Requests\ProductCreateRequest;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpecAttributes;
use Modules\Product\Rules\AttributeInCombinationUniqueRule;
use Modules\Product\Storages\ProductImages\LocalProductImagesStorage;
use Modules\Product\Storages\ProductImages\Size;
use Modules\Warehouse\Database\Seeders\WarehouseDatabaseSeeder;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;
use Modules\Warehouse\Models\ProductVariation;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            ProductDatabaseSeeder::class,
            CategoryDatabaseSeeder::class,
            WarehouseDatabaseSeeder::class,
        ]);
    }

    public function test_product_created_with_single_attributes(): void
    {
        $request = $this->createRequest('Title 2', false);

        (new CreateProductFactoryAction)
            ->createAction($request)
            ->handle(
                new CreateProduct,
                new CreateProductInWarehouse,
            );

        $product = Product::query()->where('slug', 'title-2')->first();

        $this->assertDatabaseHas('products', ['slug' => 'title-2']);

        $this->assertDatabaseHas('warehouses', ['product_id' => $product->id]);

        $this->assertDatabaseHas('product_attribute_values', ['product_id' => $product->id]);
    }

    public function test_uploading_product_images_saves_files_and_updates_model(): void
    {
        $product = Product::query()->create([
            'title' => 'Abra cadabra',
            'title_description' => 'example',
            'main_description_markdown' => 'example',
            'category_id' => Category::query()->first(['id'])->id,
            'brand_id' => Brand::query()->first(['id'])->id,
        ]);

        Storage::fake('public_products_images');

        $images = collect([
            new MainImageDto(
                order: 1,
                image: UploadedFile::fake()->image('product_image1.png'),
            ),
            new MainImageDto(
                order: 2,
                image: UploadedFile::fake()->image('product_image2.png'),
            ),
        ]);

        $dto = new ProductImageDto($images, UploadedFile::fake()->image('product_preview_image.png'));

        $storage = Mockery::mock(
            LocalProductImagesStorage::class,
            [new ImageManager(Driver::class), Storage::disk('public_products_images')],
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $storage->shouldReceive('saveResized');

        (new ProductImagesService($storage))->upload($dto, $product, new Size(100, 100));

        foreach ($images as $image) {
            Storage::disk('public_products_images')
                ->assertExists("product-id-$product->id-images/".$image->image->hashName());
        }
    }

    public function test_product_created_with_combined_attributes(): void
    {
        $request = $this->createRequest('Title 1', true);

        (new CreateProductFactoryAction)
            ->createAction($request)
            ->handle(
                new CreateProduct,
                new CreateProductInWarehouse,
            );

        $product = Product::query()->where('slug', 'title-1')->first();

        $variation = ProductVariation::query()->where('product_id', $product->id)->first();

        $this->assertDatabaseHas('products', ['slug' => 'title-1']);

        $this->assertDatabaseHas('warehouses', ['product_id' => $product->id]);

        $this->assertDatabaseHas('product_variations', ['product_id' => $product->id]);

        $this->assertDatabaseHas('variation_attribute_values', ['variation_id' => $variation->id, 'value' => 'test']);
    }

    public function test_product_created_without_attributes(): void
    {
        $request = $this->createRequest('Title 3', null);

        (new CreateProductFactoryAction)
            ->createAction($request)
            ->handle(
                new CreateProduct,
                new CreateProductInWarehouse,
            );

        $this->assertDatabaseHas('products', ['slug' => 'title-3']);

        $product = Product::query()->where('slug', 'title-3')->first(['id']);

        $this->assertDatabaseHas('warehouses', ['product_id' => $product->id]);
    }

    public function test_cannot_create_product_with_same_attributes_names_in_combination(): void
    {
        $combinedVariation = [
            [
                'sku' => 'ADADAD12',
                'quantity' => 20,
                'price' => 50.5,
                'attributes' => [
                    [
                        'id' => 1,
                        'name' => 'Color',
                        'type' => ProductAttributeTypeEnum::COLOR->value,
                        'value' => 'test',
                    ],
                    [
                        'id' => null,
                        'name' => 'color',
                        'type' => ProductAttributeTypeEnum::COLOR->value,
                        'value' => 'test',
                    ],
                ],
            ],
        ];

        foreach ($combinedVariation as $attributes) {
            $validator = Validator::make($attributes, [
                'attributes' => new AttributeInCombinationUniqueRule,
            ]);

            $this->assertFalse($validator->passes());
        }
    }

    public function test_cannot_create_product_with_same_attributes_id_in_combination(): void
    {
        $combinedVariation = [
            [
                'sku' => 'ADADAD12',
                'quantity' => 20,
                'price' => 50.5,
                'attributes' => [
                    [
                        'id' => 1,
                        'name' => 'Color',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                        ],
                        'value' => 'test',
                        'attribute_view_type' => 0,
                    ],
                    [
                        'id' => 1,
                        'name' => 'color',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                        ],
                        'value' => 'test',
                        'attribute_view_type' => 0,
                    ],
                ],
            ],
        ];

        foreach ($combinedVariation as $attributes) {
            $validator = Validator::make($attributes, [
                'attributes' => new AttributeInCombinationUniqueRule,
            ]);

            $this->assertFalse($validator->passes());
        }
    }

    public function test_throws_exception_required_attribute_not_present_in_single_variation_product(): void
    {
        $category = Category::query()->create(['name' => 'Category with attr']);

        $category->productAttributes()->create([
            'name' => 'Attr1',
            'type' => ProductAttributeTypeEnum::STRING->value,
            'required' => true,
        ]);

        $singleVariation = [
            [
                'attribute_name' => 'Size', // not required Attr1,
                'attribute_type' => 0,
                'attribute_view_type' => 0,
                'attributes' => [
                    [
                        'quantity' => 50,
                        'price' => 100,
                        'value' => 'M',
                    ],
                    [
                        'quantity' => 50,
                        'price' => 200,
                        'value' => 'L',
                    ],
                ],
            ],
        ];

        $request = $this->createRequest(
            title: 'Title 241',
            isCombinedAttributes: false,
            singleVariation: $singleVariation,
            category: $category,
        );

        $this->assertThrows(
            test: function () use ($request) {
                (new CreateProductFactoryAction)
                    ->createAction($request)
                    ->handle(
                        new CreateProduct,
                        new CreateProductInWarehouse,
                    );
            },
            expectedClass: ValidationException::class,
            expectedMessage: 'Combination must have all required attributes. See more in category section.',
        );
    }

    public function test_cannot_add_product_when_required_attributes_more_than_attributes_at_all(): void
    {
        $category = Category::query()->create(['name' => 'Category with attr2']);

        $category->productAttributes()->createMany([
            [
                'name' => 'Attr1',
                'type' => ProductAttributeTypeEnum::STRING->value,
                'required' => true,
            ],
            [
                'name' => 'Attr2',
                'type' => ProductAttributeTypeEnum::STRING->value,
                'required' => false,
            ],
            [
                'name' => 'Attr3',
                'type' => ProductAttributeTypeEnum::STRING->value,
                'required' => true,
            ],
        ]);

        $combinedVariations = [
            [
                'sku' => 'ADADAD6',
                'quantity' => 20,
                'price' => 50.5,
                'attributes' => [
                    [
                        'name' => 'Attr1',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                        ],
                        'value' => 'test',
                        'attribute_view_type' => 0,
                    ],
                    [
                        'name' => 'attr3',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                        ],
                        'value' => 'test',
                        'attribute_view_type' => 0,
                    ],
                ],
            ],
            [
                'sku' => 'ADA5D',
                'quantity' => 20,
                'price' => 50.5,
                'attributes' => [ // required attr1 and attr3 are not presented
                    [
                        'name' => 'attr2',
                        'type' => [
                            'id' => ProductAttributeTypeEnum::COLOR->value,
                        ],
                        'value' => 'test',
                        'attribute_view_type' => 0,
                    ],
                ],
            ],
        ];

        $request = $this->createRequest(
            title: 'Title 2412',
            isCombinedAttributes: true,
            combinedVariations: $combinedVariations,
            category: $category,
        );

        $this->assertThrows(
            test: function () use ($request) {
                (new CreateProductFactoryAction)
                    ->createAction($request)
                    ->handle(
                        new CreateProduct,
                        new CreateProductInWarehouse,
                    );
            },
            expectedClass: ValidationException::class,
            expectedMessage: 'Combination must have all required attributes. See more in category section.',
        );
    }

    private function createRequest(
        string $title,
        ?bool $isCombinedAttributes,
        array $combinedVariations = [],
        array $singleVariation = [],
        ?Category $category = null,
    ) {
        if (! $combinedVariations) {
            $combinedVariations = [
                [
                    'sku' => 'ADADAD',
                    'quantity' => 20,
                    'price' => 50.5,
                    'attributes' => [
                        [
                            'name' => 'Color',
                            'type' => [
                                'id' => ProductAttributeTypeEnum::COLOR->value,
                                'name' => ProductAttributeTypeEnum::COLOR->name,
                            ],
                            'value' => 'test',
                            'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                        ],
                    ],
                ],
                [
                    'sku' => 'ADAD',
                    'quantity' => 20,
                    'price' => 50.5,
                    'attributes' => [
                        [
                            'name' => 'Color',
                            'type' => [
                                'id' => ProductAttributeTypeEnum::COLOR->value,
                                'name' => ProductAttributeTypeEnum::COLOR->name,
                            ],
                            'value' => 'test',
                            'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                        ],
                    ],
                ],
            ];
        }

        if (! $singleVariation) {
            $singleVariation = [
                [
                    'attribute_name' => 'Size',
                    'attribute_type' => ProductAttributeTypeEnum::STRING->value,
                    'attribute_view_type' => ProductAttributeViewTypeEnum::RADIO_BUTTON->value,
                    'attributes' => [
                        [
                            'quantity' => 50,
                            'price' => 100,
                            'value' => 'M',
                        ],
                        [
                            'quantity' => 50,
                            'price' => 200,
                            'value' => 'L',
                        ],
                    ],
                ],
            ];
        }

        $request = Mockery::mock(ProductCreateRequest::class);

        if ($isCombinedAttributes === true) {
            $request
                ->shouldReceive('relation')
                ->with('product_variations_combinations')
                ->andReturn(collect($combinedVariations));

            $request
                ->shouldReceive('relation')
                ->with('product_variation')
                ->andReturn(collect());
        } else {
            if ($isCombinedAttributes === false) {
                $request
                    ->shouldReceive('relation')
                    ->with('product_variations_combinations')
                    ->andReturn(collect());

                $request
                    ->shouldReceive('relation')
                    ->with('product_variation')
                    ->andReturn(collect($singleVariation));
            } else {
                $request
                    ->shouldReceive('relation')
                    ->with('product_variations_combinations')
                    ->andReturn(collect());

                $request
                    ->shouldReceive('relation')
                    ->with('product_variation')
                    ->andReturn(collect());
            }
        }

        $request
            ->shouldReceive('relation')
            ->with('category')
            ->andReturn(['id' => $category->id ?? Category::query()->create(['name' => 'Test Category'])->id]);

        $request
            ->shouldReceive('relation')
            ->with('brand')
            ->andReturn(['id' => Brand::query()->first(['id'])->id]);

        $request
            ->shouldReceive('relation')
            ->with('product_specs')
            ->andReturn(
                collect([
                    [
                        'group' => 'Example',
                        'specifications' => [
                            [
                                'id' => ProductSpecAttributes::query()->create(['spec_name' => 'Example spec'])->id,
                                'spec_name' => 'Example spec',
                                'value' => ['test value'],
                            ],
                        ],
                    ],
                ]),
            );

        $request->is_combined_attributes = $isCombinedAttributes;
        $request->title = $title;
        $request->title_description = 'bla bla';
        $request->main_description = '# bla bla';
        $request->total_quantity = 100;
        $request->price = 200.1;
        $request->product_article = 'ADADAD';

        return $request;
    }
}
