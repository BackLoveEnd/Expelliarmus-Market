<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Manager\Models\Manager;
use Modules\Product\Models\Product;
use Modules\User\Database\Seeders\UserPermissionSeeder;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\Enums\ProductStatusEnum;

class ProductPublishedUnpublishedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([UserPermissionSeeder::class]);
    }

    public function test_can_publish_product(): void
    {
        $product = Product::factory()->unPublished()->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/publish");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatusEnum::PUBLISHED->value,
        ]);
    }

    public function test_cannot_publish_trashed_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::TRASHED->value])
            ->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/publish");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatusEnum::TRASHED->value,
        ]);

        $response
            ->assertStatus(403)
            ->assertExactJson(['message' => 'Cannot publish trashed product.']);
    }

    public function test_cannot_publish_already_published_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::PUBLISHED->value])
            ->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/publish");

        $response
            ->assertStatus(200)
            ->assertExactJson(['message' => 'Product is already published.']);
    }

    public function test_can_unpublish_product(): void
    {
        $product = Product::factory()->unPublished()->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/unpublish");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatusEnum::NOT_PUBLISHED->value,
        ]);
    }

    public function test_cannot_unpublish_trashed_product(): void
    {
        $product = Product::factory()->state(['status' => ProductStatusEnum::TRASHED])->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/unpublish");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatusEnum::TRASHED->value,
        ]);

        $response
            ->assertStatus(403)
            ->assertExactJson(['message' => 'Cannot unpublish trashed product.']);
    }

    public function test_can_unpublish_already_unpublished_product(): void
    {
        $product = Product::factory()->unPublished()->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $response = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/products/{$product->id}/unpublish");

        $response
            ->assertStatus(200)
            ->assertExactJson(['message' => 'Product is already unpublished.']);
    }
}
