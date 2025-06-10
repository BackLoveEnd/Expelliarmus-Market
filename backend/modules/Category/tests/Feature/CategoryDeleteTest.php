<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Category\Http\Management\Actions\DeleteCategoryAttributeAction;
use Modules\Category\Http\Management\Exceptions\AttributeNotRelatedToCategoryException;
use Modules\Category\Models\Category;
use Modules\Manager\Models\Manager;
use Modules\User\Database\Seeders\UserPermissionSeeder;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class CategoryDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([UserPermissionSeeder::class]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::query()->create(['name' => 'Category 1']);

        $manager = Manager::factory()->superManager()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);

        $this->actingAs($manager, RolesEnum::MANAGER->toString())->delete('/api/management/categories/'.$category->id);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_delete_attributes_in_category(): void
    {
        $category = Category::query()->create(['name' => 'Category 2']);

        $attributes = $this->createTestAttribute($category);

        $this->assertDatabaseHas('product_attributes', [
            'category_id' => $category->id,
            'name' => 'Attribute 1',
        ]);

        (new DeleteCategoryAttributeAction)->handle($category, $attributes->first());

        $this->assertDatabaseMissing('product_attributes', [
            'id' => $attributes->first()->id,
        ]);
    }

    public function test_will_not_delete_attribute_of_not_related_category(): void
    {
        $category = Category::query()->create(['name' => 'Category 3']);

        $this->createTestAttribute($category);

        $fakeCategory = Category::query()->create(['name' => 'Category 4']);

        $fakeAttribute = $this->createTestAttribute($fakeCategory);

        $this->assertThrows(
            test: fn () => (new DeleteCategoryAttributeAction)->handle(
                $category,
                $fakeAttribute->first(),
            ),
            expectedClass: AttributeNotRelatedToCategoryException::class,
        );
    }

    private function createTestAttribute(Category $category): Collection
    {
        return $category->productAttributes()->createMany([
            [
                'name' => 'Attribute 1',
                'type' => ProductAttributeTypeEnum::NUMBER->value,
                'required' => false,
            ],
        ]);
    }
}
