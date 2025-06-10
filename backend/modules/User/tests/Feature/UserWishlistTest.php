<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;

class UserWishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_product_wishlist_to_user(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('api/user/wishlist/products/'.$product->id);

        $response->assertExactJson([
            'message' => 'Product was added to wishlist.',
        ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_can_get_wishlist_data(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $user = User::factory()->create();

        $this->actingAs($user)->postJson('api/user/wishlist/products/'.$product->id);

        $response = $this->actingAs($user)->getJson('api/user/wishlist');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'attributes' => [
                        'id',
                        'title',
                        'slug',
                        'preview_image',
                    ],
                ],
            ],
            'meta' => [
                'total',
            ],
            'links' => [
                'next_page_number',
            ],
        ]);
    }

    public function test_can_remove_product_from_wishlist(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $user = User::factory()->create();

        $this->actingAs($user)->postJson('api/user/wishlist/products/'.$product->id);

        $response = $this
            ->actingAs($user)
            ->deleteJson('api/user/wishlist/products/'.$product->id);

        $response->assertExactJson([
            'message' => 'Product was removed from wishlist.',
        ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_can_clear_all_wishlist(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product2 = Product::factory()->published()->withoutAttributes();

        $user = User::factory()->create();

        $this->actingAs($user)->postJson('api/user/wishlist/products/'.$product->id);

        $this->actingAs($user)->postJson('api/user/wishlist/products/'.$product2->id);

        $response = $this->actingAs($user)->deleteJson('api/user/wishlist/products');

        $response->assertExactJson([
            'message' => 'Wishlist was cleared.',
        ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_add_product_if_it_not_published(): void
    {
        $product = Product::factory()->withoutAttributes();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('api/user/wishlist/products/'.$product->id);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'message' => 'Cannot add not published product to wishlist.',
            ]);
    }

    public function test_cannot_remove_product_from_someone_else_product(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $user1 = User::factory()->create();

        $user2 = User::factory()->create();

        $this->actingAs($user1)->postJson('api/user/wishlist/products/'.$product->id);

        $response = $this
            ->actingAs($user2)
            ->deleteJson('api/user/wishlist/products/'.$product->id);

        $response->assertExactJson([
            'message' => 'This product does not exists in your wishlist.',
        ]);
    }

    public function test_cannot_add_product_that_already_in_wishlist(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $user1 = User::factory()->create();

        $this->actingAs($user1)->postJson('api/user/wishlist/products/'.$product->id);

        $response = $this->actingAs($user1)->postJson('api/user/wishlist/products/'.$product->id);

        $response->assertExactJson([
            'message' => 'Product is already in wishlist.',
        ]);
    }
}
