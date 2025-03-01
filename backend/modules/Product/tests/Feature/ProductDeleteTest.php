<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Http\Management\Actions\Product\Edit\MoveProductToTrashAction;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_can_restore_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::TRASHED])->withoutAttributes();

        // move product to trash

        $this->deleteJson("api/management/products/{$product->id}/trash");

        //restore product from trash
        $this->postJson("api/management/products/{$product->id}/restore");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
            'status' => ProductStatusEnum::NOT_PUBLISHED->value,
        ]);
    }

    public function test_can_permanently_delete_product_from_trash(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::TRASHED])->withoutAttributes();

        // move product to trash
        (new MoveProductToTrashAction())->handle($product);

        //permanently delete product from trash
        $this->deleteJson("api/management/products/{$product->id}");

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        $this->assertDatabaseMissing('warehouses', [
            'id' => $product->id,
        ]);
    }

    public function test_cannot_restore_not_trashed_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::PUBLISHED])->withoutAttributes();

        $response = $this->postJson("api/management/products/{$product->id}/restore");

        $response
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Cannot restore not trashed product.']);
    }

    public function test_cannot_permanently_delete_not_trashed_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::PUBLISHED])->withoutAttributes();

        $response = $this->deleteJson("api/management/products/{$product->id}");

        $response
            ->assertStatus(403)
            ->assertExactJson(['message' => 'Force deleting not trashed product is not allowed.']);
    }
}