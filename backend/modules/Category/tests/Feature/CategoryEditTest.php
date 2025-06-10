<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Mockery;
use Modules\Category\Http\Management\Actions\EditCategoryAction;
use Modules\Category\Http\Management\DTO\EditCategoryDto;
use Modules\Category\Http\Management\Requests\EditCategoryRequest;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class CategoryEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_edit_category(): void
    {
        $category = Category::query()->create(['name' => 'Category 1']);

        $request = $this->createRequest($category, 'Edited Category 1', [
            [
                'name' => 'Test Attribute',
                'type' => ProductAttributeTypeEnum::NUMBER->value,
                'required' => false,
            ],
        ]);

        $dto = EditCategoryDto::fromRequest($request);

        (new EditCategoryAction)->handle($dto);

        $this->assertDatabaseHas('categories', [
            'name' => 'Edited Category 1',
        ]);

        $this->assertDatabaseHas('product_attributes', [
            'category_id' => $category->id,
        ]);
    }

    public function test_will_save_only_new_attributes_for_category(): void
    {
        $category = Category::query()->create(['name' => 'Category 1']);

        $category->productAttributes()->createMany([
            [
                'name' => 'Test attribute',
                'type' => ProductAttributeTypeEnum::NUMBER->value,
                'required' => false,
            ],
        ]);

        $newAttributes = [
            [
                'name' => 'Test attribute',
                'type' => ProductAttributeTypeEnum::NUMBER->value,
                'required' => false,
            ],
            [
                'name' => 'New test attribute',
                'type' => ProductAttributeTypeEnum::COLOR->value,
                'required' => false,
            ],
        ];

        $request = $this->createRequest($category, 'Edited Category 1', $newAttributes);

        $dto = EditCategoryDto::fromRequest($request);

        (new EditCategoryAction)->handle($dto);

        $this->assertEquals(collect($newAttributes)->pluck('name'),
            $category->productAttributes->pluck('name'));
    }

    private function createRequest(
        ?Category $category = null,
        ?string $categoryName = null,
        array $attributes = []
    ) {
        if (! $attributes) {
            $attributes = [
                [
                    'name' => 'Size',
                    'type' => ProductAttributeTypeEnum::NUMBER->value,
                    'required' => false,
                ],
            ];
        }

        $request = Mockery::mock(EditCategoryRequest::class);

        $request->shouldReceive('route')
            ->with('category')
            ->andReturn($category->id);

        $request->shouldReceive('relation')
            ->with('attributes')
            ->andReturn(collect($attributes));

        $request->name = $categoryName ?? 'Edited Category';

        return $request;
    }
}
