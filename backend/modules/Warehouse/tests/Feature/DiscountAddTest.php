<?php

declare(strict_types=1);

namespace Modules\Warehouse\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Manager\Models\Manager;
use Modules\Product\Models\Product;
use Modules\User\Database\Seeders\UserPermissionSeeder;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Http\Exceptions\VariationToApplyDiscountDoesNotExists;
use Modules\Warehouse\Services\Discount\ProductDiscountServiceFactory;

class DiscountAddTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([UserPermissionSeeder::class]);
    }

    public function test_can_add_discount_for_product_without_variations(): void
    {
        $product = Product::factory()->withoutAttributes();

        $dto = new ProductDiscountDto(
            percentage: 25,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: null,
        );

        (new ProductDiscountServiceFactory($this->app))
            ->addDiscount($product)
            ->process($dto);

        $this->assertDatabaseHas('discounts', [
            'discountable_id' => $product->id,
            'discountable_type' => Product::class,
        ]);
    }

    public function test_can_add_discount_for_product_with_single_variation(): void
    {
        $product = Product::factory()->withSingleAttributes();

        $variation = $product->singleAttributes->first();

        $dto = new ProductDiscountDto(
            percentage: 25,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: $variation->id,
        );

        (new ProductDiscountServiceFactory($this->app))
            ->addDiscount($product)
            ->process($dto);

        $this->assertDatabaseHas('discounts', [
            'discountable_id' => $variation->id,
            'discountable_type' => $variation::class,
        ]);
    }

    public function test_can_add_discount_for_product_with_combo_variations(): void
    {
        $product = Product::factory()->withCombinedAttributes();

        $variation = $product->combinedAttributes->first();

        $dto = new ProductDiscountDto(
            percentage: 25,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: $variation->id,
        );

        (new ProductDiscountServiceFactory($this->app))
            ->addDiscount($product)
            ->process($dto);

        $this->assertDatabaseHas('discounts', [
            'discountable_id' => $variation->id,
            'discountable_type' => $variation::class,
        ]);
    }

    public function test_cannot_add_discount_for_non_exist_variation(): void
    {
        $product = Product::factory()->withSingleAttributes();

        $dto = new ProductDiscountDto(
            percentage: 25,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: 124, // fake variation id
        );

        $this->assertThrows(
            test: fn () => (new ProductDiscountServiceFactory($this->app))
                ->addDiscount($product)
                ->process($dto),
            expectedClass: VariationToApplyDiscountDoesNotExists::class,
        );
    }

    public function test_cannot_add_discount_with_wrong_dates(): void
    {
        $product = Product::factory()->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        $response1 = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->postJson("api/management/warehouse/products/$product->id/discounts", [
                'data' => [
                    'attributes' => [
                        'percentage' => 25,
                        'start_date' => Carbon::now()->subDay(),
                        'end_date' => Carbon::now()->addDay(),
                        'variation' => null,
                    ],
                ],
            ]);

        $response1->assertJsonValidationErrors([
            'data.attributes.start_date' => 'The date has already passed.',
        ]);

        $response2 = $this->postJson("api/management/warehouse/products/$product->id/discounts", [
            'data' => [
                'attributes' => [
                    'percentage' => 25,
                    'start_date' => Carbon::now()->addDay(),
                    'end_date' => Carbon::now()->subDay(),
                    'variation' => null,
                ],
            ],
        ]);

        $response2->assertJsonValidationErrors([
            'data.attributes.end_date' => 'The end date must be after start date.',
        ]);
    }
}
