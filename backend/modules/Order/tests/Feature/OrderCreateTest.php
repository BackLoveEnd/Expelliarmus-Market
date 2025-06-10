<?php

declare(strict_types=1);

namespace Modules\Order\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Order\Cart\Models\Cart;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Product\Models\Product;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\Discount;

class OrderCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_as_regular_user(): void
    {
        $product1 = Product::factory()->published()->withoutAttributes();
        $product2 = Product::factory()->published()->withSingleAttributes();
        $product3 = Product::factory()->published()->withCombinedAttributes();

        $user = User::factory()->create();

        $cartItem1 = Cart::factory()
            ->product($product1)
            ->forUser($user)
            ->create();

        $cartItem2 = Cart::factory()
            ->product($product2, $product2->singleAttributes->first())
            ->forUser($user)
            ->create();

        $cartItem3 = Cart::factory()
            ->product($product3, $product3->combinedAttributes->first())
            ->forUser($user)
            ->create();

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $response->assertJsonFragment([
            'message' => 'Order created successfully.',
        ]);

        $this->assertDatabaseHas('orders', [
            'userable_id' => $user->id,
            'userable_type' => User::class,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $orderId = $user->orders()->first()->id;

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product1->id,
            'quantity' => $cartItem1->quantity,
            'total_price' => $cartItem1->final_price,
            'variation' => null,
            'price_per_unit_at_order_time' => $cartItem1->price_per_unit,
        ]);

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product2->id,
            'quantity' => $cartItem2->quantity,
            'total_price' => $cartItem2->final_price,
            'variation->id' => $cartItem2->variation['id'],
            'price_per_unit_at_order_time' => $cartItem2->price_per_unit,
        ]);

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product3->id,
            'quantity' => $cartItem3->quantity,
            'total_price' => $cartItem3->final_price,
            'variation->id' => $cartItem3->variation['id'],
            'price_per_unit_at_order_time' => $cartItem3->price_per_unit,
        ]);

        // Check if the stock was decreased
        $this->assertDatabaseHas('warehouses', [
            'product_id' => $product1->id,
            'total_quantity' => $product1->warehouse->total_quantity - $cartItem1->quantity,
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product2->id,
            'quantity' => $product2->singleAttributes->first()->quantity - $cartItem2->quantity,
        ]);

        $this->assertDatabaseHas('product_variations', [
            'product_id' => $product3->id,
            'quantity' => $product3->combinedAttributes->first()->quantity - $cartItem3->quantity,
        ]);
    }

    public function test_can_create_order_as_guest_user(): void
    {
        $product1 = Product::factory()->published()->withoutAttributes();
        $product2 = Product::factory()->published()->withSingleAttributes();

        $variationProduct2 = $product2->singleAttributes->first();

        $responseFirstCart = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product1->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $responseSecondCart = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product2->id,
                    'variation_id' => $variationProduct2->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        // As guest user
        $response = $this->postJson('/api/shop/user/orders/checkout', [
            'data' => [
                'type' => 'guests',
                'attributes' => [
                    'first_name' => 'Ihor',
                    'last_name' => 'K',
                    'email' => 'testorderihor@gmail.com',
                    'phone' => '+380915155577',
                    'address' => 'Kyiv, Ukraine',
                ],
            ],
        ]);

        $responseFirstCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $responseSecondCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $this->assertDatabaseHas('guests', [
            'email' => 'testorderihor@gmail.com',
        ]);

        $guest = Guest::query()->whereEmail('testorderihor@gmail.com')->first();

        $response->assertJsonFragment([
            'message' => 'Order created successfully.',
        ]);

        $this->assertDatabaseHas('orders', [
            'userable_id' => $guest->id,
            'userable_type' => Guest::class,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $orderId = $guest->orders()->first()->id;

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product1->id,
            'quantity' => 3,
            'total_price' => round($product1->warehouse->default_price * 3, 2),
            'variation' => null,
            'price_per_unit_at_order_time' => $product1->warehouse->default_price,
        ]);

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product2->id,
            'quantity' => 1,
            'total_price' => round($variationProduct2->price, 2),
            'variation->id' => $variationProduct2->id,
            'price_per_unit_at_order_time' => $variationProduct2->price,
        ]);
    }

    public function test_can_create_order_with_product_that_has_discount(): void
    {
        $product = Product::factory()->published()->withoutAttributes();

        $productWithDiscount = Product::factory()->published()->withCombinedAttributes();
        $discountProduct = Discount::factory()
            ->product($productWithDiscount)
            ->create();

        $combinedVariation = $productWithDiscount->combinedAttributes->first();

        $user = User::factory()->create();

        $responseFirstCart = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $responseSecondCart = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $productWithDiscount->id,
                    'variation_id' => $combinedVariation->id, // That variation has discount
                    'quantity' => 2,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $responseFirstCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $responseSecondCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $response->assertJsonFragment([
            'message' => 'Order created successfully.',
        ]);

        $this->assertDatabaseHas('orders', [
            'userable_id' => $user->id,
            'userable_type' => User::class,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $orderId = $user->orders()->first()->id;

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 3,
            'total_price' => round($product->warehouse->default_price * 3, 2),
            'variation' => null,
            'price_per_unit_at_order_time' => $product->warehouse->default_price,
        ]);

        $this->assertDatabaseHas('order_lines', [
            'order_id' => $orderId,
            'product_id' => $productWithDiscount->id,
            'quantity' => 2,
            'total_price' => round($discountProduct->discount_price * 2, 2), // price with discount
            'variation->id' => $combinedVariation->id,
            'price_per_unit_at_order_time' => $discountProduct->discount_price,
        ]);
    }

    public function test_can_create_order_with_global_coupon(): void
    {
        $product1 = Product::factory()->published()->withoutAttributes();
        $product2 = Product::factory()->published()->withSingleAttributes();

        $variationProduct2 = $product2->singleAttributes->first();

        $coupon = Coupon::factory()->create();

        $responseFirstCart = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product1->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $responseSecondCart = $this->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product2->id,
                    'variation_id' => $variationProduct2->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->postJson('/api/shop/user/orders/checkout', [
            'data' => [
                'type' => 'guests',
                'attributes' => [
                    'first_name' => 'Ihor',
                    'last_name' => 'K',
                    'email' => 'testorderihor@gmail.com',
                    'phone' => '+380915155577',
                    'address' => 'Kyiv, Ukraine',
                    'coupon' => $coupon->coupon_id,
                ],
            ],
        ]);

        $responseFirstCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $responseSecondCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $this->assertDatabaseHas('guests', [
            'email' => 'testorderihor@gmail.com',
        ]);

        $guest = Guest::query()->whereEmail('testorderihor@gmail.com')->first();

        $response->assertJsonFragment([
            'message' => 'Order created successfully.',
        ]);

        $this->assertDatabaseHas('orders', [
            'userable_id' => $guest->id,
            'userable_type' => Guest::class,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $this->assertDatabaseHas('coupon_user', [
            'email' => $guest->email,
            'user_id' => null,
            'coupon_id' => $coupon->id,
            'usage_number' => 1,
        ]);

        $order = $guest->orders()->first();

        $totalPrice = $product1->warehouse->default_price * 3 + $variationProduct2->price;

        $this->assertEquals(
            round((float) $order->total_price, 2),
            round($totalPrice - ($totalPrice * $coupon->discount / 100), 2),
        );
    }

    public function test_can_create_order_with_personal_coupon(): void
    {
        $product1 = Product::factory()->published()->withoutAttributes();
        $product2 = Product::factory()->published()->withSingleAttributes();

        $variationProduct2 = $product2->singleAttributes->first();

        $user = User::factory()->create();

        $coupon = Coupon::factory()->user($user)->type(CouponTypeEnum::PERSONAL)->create();

        $responseFirstCart = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product1->id,
                    'variation_id' => null,
                    'quantity' => 3,
                ],
            ],
        ]);

        $responseSecondCart = $this->actingAs($user)->postJson('api/shop/user/cart', [
            'data' => [
                'attributes' => [
                    'product_id' => $product2->id,
                    'variation_id' => $variationProduct2->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout', [
            'data' => [
                'type' => 'guests',
                'attributes' => [
                    'coupon' => $coupon->coupon_id,
                ],
            ],
        ]);

        $responseFirstCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $responseSecondCart->assertExactJson([
            'message' => 'Product was added to cart.',
        ]);

        $this->assertDatabaseHas('orders', [
            'userable_id' => $user->id,
            'userable_type' => User::class,
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $this->assertDatabaseMissing('coupon_user', [
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
        ]);

        $order = $user->orders()->first();

        $totalPrice = $product1->warehouse->default_price * 3 + $variationProduct2->price;

        $this->assertEquals(
            round((float) $order->total_price, 2),
            round($totalPrice - ($totalPrice * $coupon->discount / 100), 2),
        );
    }

    public function test_cannot_create_order_if_user_cart_empty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $response->assertStatus(409);

        $response->assertExactJson([
            'message' => 'Cart must not be empty before order.',
        ]);
    }

    public function test_cannot_create_order_if_product_unpublished(): void
    {
        $notPublishedProduct = Product::factory()->unPublished()->withoutAttributes();

        $user = User::factory()->create();

        $cartItem = Cart::factory()
            ->product($notPublishedProduct)
            ->forUser($user)
            ->create();

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $response
            ->assertExactJson([
                'message' => 'Product currently cannot be processed to checkout.',
            ])
            ->assertStatus(422);
    }

    public function test_cannot_create_order_if_product_has_not_in_stock_status(): void
    {
        $notInStockProduct = Product::factory()->published()->withoutAttributes();

        $notInStockProduct->warehouse->update(['status' => WarehouseProductStatusEnum::NOT_AVAILABLE->value]);

        $user = User::factory()->create();

        $cartItem = Cart::factory()
            ->product($notInStockProduct)
            ->forUser($user)
            ->create();

        $response1 = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $response1
            ->assertExactJson([
                'message' => 'Product currently cannot be processed to checkout.',
            ])
            ->assertStatus(422);
    }

    public function test_cannot_create_order_if_product_does_not_have_enough_supplies(): void
    {
        $chosenVariationOutOfStock = Product::factory()->published()->withCombinedAttributes();
        $variation = $chosenVariationOutOfStock->combinedAttributes
            ->first();

        $user = User::factory()->create();

        $cartItem = Cart::factory()
            ->product($chosenVariationOutOfStock, $variation)
            ->forUser($user)
            ->create();

        $variation->update(['quantity' => 0]);

        $response = $this->actingAs($user)->postJson('/api/shop/user/orders/checkout');

        $response
            ->assertExactJson([
                'message' => "Product $chosenVariationOutOfStock->product_article has not enough supplies for requested quantities.",
            ])
            ->assertStatus(409);
    }
}
