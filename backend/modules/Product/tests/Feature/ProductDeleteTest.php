<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class ProductDeleteTest extends TestCase
{
    public function test_can_trash_product(): void
    {
        $product = Product::factory()->unPublished()->withoutAttributes();

        $this->deleteJson("api/management/products/{$product->id}/trash");

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
            'status' => ProductStatusEnum::TRASHED->value,
        ]);
    }

    public function test_cannot_trash_trashed_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::TRASHED])->withoutAttributes();

        $response = $this->deleteJson("api/management/products/{$product->id}/trash");

        $response
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Product is already in trash.']);
    }

    public function test_cannot_trash_published_product(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $response = $this->deleteJson("api/management/products/{$product->id}/trash");

        $response
            ->assertStatus(403)
            ->assertExactJson(['message' => 'Moving published product to trash is not allowed.']);
    }
}