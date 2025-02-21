<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Brand\Database\Seeders\BrandDatabaseSeeder;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class BrandManipulationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([BrandDatabaseSeeder::class]);
    }

    public function test_can_create_brand(): void
    {
        $response = $this->postJson('api/management/brands', [
            'data' => [
                'type' => 'brands',
                'attributes' => [
                    'name' => 'Test Brand 1',
                    'description' => null
                ]
            ]
        ]);

        $response->assertJsonPath('data.attributes.brand_name', 'Test Brand 1');
    }

    public function test_can_edit_brand(): void
    {
        $brand = Brand::query()->first();

        $response = $this->putJson("api/management/brands/$brand->id", [
            'data' => [
                'type' => 'brands',
                'attributes' => [
                    'name' => 'Test Brand 2',
                    'description' => null
                ]
            ]
        ]);

        $brand->refresh();

        $response->assertJsonPath('data.slug', $brand->slug);
    }

    public function test_can_delete_brand(): void
    {
        $brand = Brand::query()->first();

        $this->delete("api/management/brands/{$brand->id}");

        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id
        ]);
    }

    public function test_throw_error_if_brand_has_products(): void
    {
        $brand = Brand::query()->create(['name' => 'Test Brand 3']);

        $product = Product::query()->create([
            'title' => 'test123',
            'category_id' => Category::query()->create(['name' => 'Test Category 1'])->id,
            'brand_id' => $brand->id,
            'title_description' => 'test',
            'main_description_markdown' => 'test',
            'product_article' => 'test123',
        ]);

        $response = $this->delete("api/management/brands/{$brand->id}");

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson(['message' => 'Failed to delete brand: brand has products.']);
    }
}