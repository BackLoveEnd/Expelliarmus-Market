<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Category\Http\Management\Actions\SaveCategoryWithAttributesAction;
use Modules\Category\Http\Management\DTO\CreateCategoryDto;
use Modules\Category\Http\Management\Exceptions\AttributesMustBeUniqueForCategoryException;
use Modules\Category\Http\Management\Requests\CreateCategoryRequest;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class CategoryCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_new_category_with_attributes(): void
    {
        $request = $this->createRequest(categoryName: 'Category 1');

        $dto = CreateCategoryDto::fromRequest($request);

        $category = (new SaveCategoryWithAttributesAction)->handle($dto);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('product_attributes', [
            'category_id' => $category->id,
        ]);
    }

    public function test_can_create_subcategory_with_attributes(): void
    {
        $existedCategory = Category::query()->create(['name' => 'Category Root']);

        $request = $this->createRequest(categoryName: 'Category 2', parent: $existedCategory->id);

        $dto = CreateCategoryDto::fromRequest($request);

        $category = (new SaveCategoryWithAttributesAction)->handle($dto);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $existedCategory->id,
        ]);

        $this->assertDatabaseHas('product_attributes', [
            'category_id' => $category->id,
        ]);
    }

    public function test_will_not_create_category_if_attributes_not_unique_in_tree(): void
    {
        $existedCategory = Category::query()->create(['name' => 'Root test category']);

        $testAttributes = $this->createTestAttribute($existedCategory);

        $request = $this->createRequest(
            categoryName: 'Category 2',
            parent: $existedCategory->id,
            attributes: $testAttributes
        );

        $dto = CreateCategoryDto::fromRequest($request);

        $this->assertThrows(
            test: fn () => (new SaveCategoryWithAttributesAction)->handle($dto),
            expectedClass: AttributesMustBeUniqueForCategoryException::class
        );
    }

    private function createRequest(
        ?string $categoryName = null,
        ?int $parent = null,
        array $attributes = []
    ) {
        if (! $attributes) {
            $attributes = [
                [
                    'name' => 'Size',
                    'type' => ProductAttributeTypeEnum::NUMBER->value,
                    'required' => false,
                ],
                [
                    'name' => 'Color',
                    'type' => ProductAttributeTypeEnum::COLOR->value,
                    'required' => false,
                ],
            ];
        }

        $request = Mockery::mock(CreateCategoryRequest::class);

        $request->shouldReceive('relation')
            ->with('attributes')
            ->andReturn(collect($attributes));

        $request->name = $categoryName ?? 'Test Category';
        $request->parent = $parent;

        return $request;
    }

    private function createTestAttribute(Category $category): array
    {
        $attribute = [
            [
                'name' => 'Test Attribute',
                'type' => ProductAttributeTypeEnum::NUMBER->value,
                'required' => false,
            ],
        ];

        $category->productAttributes()->createMany($attribute);

        return $attribute;
    }
}
