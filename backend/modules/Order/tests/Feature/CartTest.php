<?php

declare(strict_types=1);

namespace Modules\Order\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Ramsey\Uuid\Uuid;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_add_product_with_wrong_status(): void
    {
        $product = Product::factory()->unPublished()->withoutAttributes();

        $response = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertExactJson([
            'message' => 'Product cannot be added to cart. Possibly not in stock.',
        ]);

        $product = Product::factory()->published()->withoutAttributes();

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::NOT_AVAILABLE]);

        $response = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertExactJson([
            'message' => 'Product cannot be added to cart. Possibly not in stock.',
        ]);
    }

    public function test_cannot_add_product_if_not_enough_supplies(): void
    {
        $product = Product::factory()->published()->withSingleAttributes();

        $variation = $product->singleAttributes->first();

        $variation->update(['quantity' => 0]);

        $response = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertExactJson([
            'message' => 'Product cannot be added to cart. Possibly not in stock.',
        ]);
    }

    public function test_can_add_product_for_anonymous_user_to_cart(): void
    {
        $product = Product::factory()->published()->withCombinedAttributes();

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $productVariation = $product->combinedAttributes->first();

        $productVariation->load('productAttributes');

        $response = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => $productVariation->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $response->assertSessionHas('user.cart', [
            (object) [
                'id' => session('user.cart')[0]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 2,
                'price_per_unit' => $productVariation->price,
                'final_price' => round($productVariation->price * 2, 2),
                'variation' => [
                    'id' => $productVariation->id,
                    'data' => $productVariation->productAttributes->map(fn ($item) => [
                        'attribute_name' => $item->name,
                        'value' => $item->pivot->value,
                        'type' => $item->type->toTypes(),
                    ]),
                ],
                'discount' => null,
            ],
        ]);
    }

    public function test_can_add_product_to_cart_for_auth_user(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product->loadMissing('warehouse');

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $response->assertSessionHas('user.cart', [
            (object) [
                'id' => session('user.cart')[0]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 3,
                'price_per_unit' => $product->warehouse->default_price,
                'final_price' => round($product->warehouse->default_price * 3, 2),
                'variation' => null,
                'discount' => null,
            ],
        ]);
    }

    public function test_can_get_cart_info_for_anonymous_user(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product->loadMissing('warehouse');

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->getJson('api/shop/user/cart');

        $response->assertExactJson([
            'data' => [
                [
                    'id' => session('user.cart')[0]->id,
                    'type' => 'cart',
                    'attributes' => [
                        'product_id' => $product->id,
                        'product_image' => $product->preview_image,
                        'product_title' => $product->title,
                        'product_slug' => $product->slug,
                        'quantity' => 3,
                        'price_per_unit' => $product->warehouse->default_price,
                        'final_price' => round($product->warehouse->default_price * 3, 2),
                        'variation' => null,
                        'discount' => null,
                    ],
                ],
                [
                    'id' => session('user.cart')[1]->id,
                    'type' => 'cart',
                    'attributes' => [
                        'product_id' => $product->id,
                        'product_image' => $product->preview_image,
                        'product_title' => $product->title,
                        'product_slug' => $product->slug,
                        'quantity' => 1,
                        'price_per_unit' => $product->warehouse->default_price,
                        'final_price' => round($product->warehouse->default_price * 1, 2),
                        'variation' => null,
                        'discount' => null,
                    ],
                ],
            ],
        ]);
    }

    public function test_can_get_cart_info_for_auth_user_from_db(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product->loadMissing('warehouse');

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $user = User::factory()->create();

        $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        // User lose his session
        session()->forget('user.cart');

        $response = $this->getJson('api/shop/user/cart');

        $response->assertExactJson([
            'data' => [
                [
                    'id' => session('user.cart')[0]->id,
                    'type' => 'cart',
                    'attributes' => [
                        'product_id' => $product->id,
                        'product_image' => $product->preview_image,
                        'product_title' => $product->title,
                        'product_slug' => $product->slug,
                        'quantity' => 4,
                        'price_per_unit' => round($product->warehouse->default_price, 2),
                        'final_price' => round($product->warehouse->default_price * 3, 2),
                        'variation' => null,
                        'discount' => null,
                    ],
                ],
            ],
        ]);
    }

    public function test_can_clear_all_cart_info(): void
    {
        $product = Product::factory()->published()->withSingleAttributes();

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => $product->singleAttributes->first()->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->deleteJson('api/shop/user/cart');

        $response->assertExactJson(['message' => 'Cart was cleared.']);

        $response->assertSessionMissing('user.cart');
    }

    public function test_can_remove_specific_product_from_cart(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertSessionHas('user.cart', [
            (object) [
                'id' => session('user.cart')[0]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 1,
                'price_per_unit' => $product->warehouse->default_price,
                'final_price' => round($product->warehouse->default_price * 1, 2),
                'variation' => null,
                'discount' => null,
            ],
            (object) [
                'id' => session('user.cart')[1]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 2,
                'price_per_unit' => $product->warehouse->default_price,
                'final_price' => round($product->warehouse->default_price * 2, 2),
                'variation' => null,
                'discount' => null,
            ],
        ]);

        $response = $this->deleteJson('api/shop/user/cart/'.session('user.cart')[0]->id);

        $response->assertSessionHas('user.cart', [
            (object) [
                'id' => session('user.cart')[0]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 2,
                'price_per_unit' => $product->warehouse->default_price,
                'final_price' => round($product->warehouse->default_price * 2, 2),
                'variation' => null,
                'discount' => null,
            ],
        ]);
    }

    public function test_can_update_quantities_in_cart_for_anonymous_user(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product2 = Product::factory()->published()->withoutAttributes();

        $product->loadMissing('warehouse');

        $product2->loadMissing('warehouse');

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $product2->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $user = User::factory()->create();

        $response1 = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response2 = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product2->id,
                    'variation_id' => null,
                    'quantity' => 4,
                ],
            ],
        ]);

        $cart = session()->get('user.cart', []);

        $updateResponse = $this
            ->actingAs($user)
            ->withSession(['user.cart' => $cart])
            ->patchJson('api/shop/user/cart', [
                'data' => [
                    'attributes' => [
                        'items' => [
                            [
                                'cart_id' => $cart[0]->id,
                                'quantity' => 2,
                            ],
                            [
                                'cart_id' => $cart[1]->id,
                                'quantity' => 3,
                            ],
                        ],
                    ],
                ],
            ]);

        $updateResponse->assertExactJson([
            'message' => 'Cart was updated.',
        ]);

        $cart = session()->get('user.cart', []);

        $updateResponse->assertSessionHas('user.cart', [
            (object) [
                'id' => $cart[0]->id,
                'product_id' => $product->id,
                'product_image' => $product->preview_image,
                'product_title' => $product->title,
                'product_slug' => $product->slug,
                'quantity' => 2, // New quantity
                'price_per_unit' => $product->warehouse->default_price,
                'final_price' => round($product->warehouse->default_price * 2, 2), // New price
                'variation' => null,
                'discount' => null,
            ],
            (object) [
                'id' => $cart[1]->id,
                'product_id' => $product2->id,
                'product_image' => $product2->preview_image,
                'product_title' => $product2->title,
                'product_slug' => $product2->slug,
                'quantity' => 3, // New quantity
                'price_per_unit' => $product2->warehouse->default_price,
                'final_price' => round($product2->warehouse->default_price * 3, 2), // New price
                'variation' => null,
                'discount' => null,
            ],
        ]);

        foreach ($cart as $cartItem) {
            $this->assertDatabaseHas('cart', [
                'user_id' => $user->id,
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
            ]);
        }
    }

    public function test_cannot_update_quantities_if_has_not_enough_supplies(): void
    {
        $product = Product::factory()->published()->withCombinedAttributes();

        $product->warehouse()->update(['status' => WarehouseProductStatusEnum::IN_STOCK]);

        $variation = $product->combinedAttributes->first();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('api/shop/user/cart', [
                'data' => [
                    'attributes' => [
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

        $variation->update(['quantity' => 0]);

        $response2 = $this
            ->actingAs($user)
            ->patchJson('api/shop/user/cart', [
                'data' => [
                    'attributes' => [
                        'items' => [
                            [
                                'cart_id' => session()->get('user.cart')[0]->id,
                                'quantity' => 5,
                            ],
                        ],
                    ],
                ],
            ]);

        $response2->assertExactJson([
            'message' => "Product $product->product_article has not enough supplies for requested quantities.",
        ]);
    }

    public function test_can_sync_session_with_db_cart_when_user_login()
    {
        $product = Product::factory()->published()->withoutAttributes();

        $product2 = Product::factory()->published()->withSingleAttributes();

        $sessionCart = [
            (object) [
                'id' => Uuid::uuid7()->toString(),
                'product_id' => $product->id,
                'product_title' => $product->title,
                'product_image' => $product->preview_image,
                'product_slug' => $product->slug,
                'quantity' => 2,
                'price_per_unit' => 100,
                'final_price' => 200,
                'discount' => null,
                'variation' => null,
            ],
            (object) [
                'id' => Uuid::uuid7()->toString(),
                'product_id' => $product2->id,
                'product_title' => $product2->title,
                'product_image' => $product2->preview_image,
                'product_slug' => $product2->slug,
                'quantity' => 1,
                'price_per_unit' => 150,
                'final_price' => 150,
                'discount' => null,
                'variation' => json_encode(['id' => $product2->singleAttributes->first()->id]),
            ],
        ];

        session()->put('user.cart', $sessionCart);

        $user = User::factory()->create();

        $existCart = $user->cart()->create([
            'product_id' => $product2->id,
            'quantity' => 1,
            'price_per_unit' => 150,
            'final_price' => 150,
            'discount' => null,
            'variation' => json_encode(['id' => $product2->singleAttributes->first()->id]),
        ]);

        $cartStorage = new CartStorageService(session()->driver());

        $cartStorage->syncSessionCartAfterLogin($user);

        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'id' => $existCart->id,
        ]);

        $this->assertDatabaseHas('cart', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'id' => $sessionCart[0]->id,
        ]);
    }
}
