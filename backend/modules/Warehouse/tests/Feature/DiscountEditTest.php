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
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Services\Discount\ProductDiscountServiceFactory;

class DiscountEditTest extends TestCase
{
    use RefreshDatabase;

    private ProductDiscountServiceFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ProductDiscountServiceFactory($this->app);

        $this->seed([UserPermissionSeeder::class]);
    }

    public function test_can_edit_discount(): void
    {
        $product = Product::factory()->withoutAttributes();

        $addDto = new ProductDiscountDto(
            percentage: 20,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
        );

        $discount = $this->createDiscount($product, $addDto);

        $this->assertDatabaseHas('discounts', [
            'percentage' => $addDto->percentage,
            'start_date' => $addDto->startFrom,
            'end_date' => $addDto->endAt,
            'discountable_id' => $product->id,
            'discountable_type' => Product::class,
        ]);

        $editDto = new ProductDiscountDto(
            percentage: 30,
            endAt: Carbon::now()->addDays(2),
        );

        $this->factory
            ->editDiscount($product, $discount)
            ->process($editDto);

        $this->assertDatabaseHas('discounts', [
            'percentage' => $editDto->percentage,
            'end_date' => $editDto->endAt,
            'discount_price' => $discount->original_price * (1 - ($editDto->percentage / 100)),
            'discountable_id' => $product->id,
        ]);
    }

    public function test_edited_discount_is_not_related_to_product_without_attributes(): void
    {
        $product = Product::factory()->withoutAttributes();

        $product2 = Product::factory()->withoutAttributes();

        $addDto = new ProductDiscountDto(
            percentage: 20,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
        );

        $discount = $this->createDiscount($product, $addDto);

        $editDto = new ProductDiscountDto(
            percentage: 30,
            endAt: Carbon::now()->addDays(2),
        );

        $this->assertThrows(
            test: fn () => $this->factory
                ->editDiscount($product2, $discount) // want to edit discount to wrong product
                ->process($editDto),
            expectedClass: DiscountIsNotRelatedToProductException::class,
        );
    }

    public function test_edited_discount_is_not_related_to_product_with_single_attributes(): void
    {
        $product = Product::factory()->withSingleAttributes();

        $product2 = Product::factory()->withSingleAttributes();

        $addDto = new ProductDiscountDto(
            percentage: 20,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: $product->singleAttributes->first()->id,
        );

        $discount = $this->createDiscount($product, $addDto);

        $editDto = new ProductDiscountDto(
            percentage: 30,
            endAt: Carbon::now()->addDays(2),
        );

        $this->assertThrows(
            test: fn () => $this->factory
                ->editDiscount($product2, $discount) // want to edit discount to wrong product
                ->process($editDto),
            expectedClass: DiscountIsNotRelatedToProductException::class,
        );
    }

    public function test_edited_discount_is_not_related_to_product_with_combined_attributes(): void
    {
        $product = Product::factory()->withCombinedAttributes();

        $product2 = Product::factory()->withCombinedAttributes();

        $addDto = new ProductDiscountDto(
            percentage: 20,
            endAt: Carbon::now()->addDays(2),
            startFrom: Carbon::now()->addDay(),
            variationId: $product->combinedAttributes->first()->id,
        );

        $discount = $this->createDiscount($product, $addDto);

        $editDto = new ProductDiscountDto(
            percentage: 30,
            endAt: Carbon::now()->addDays(2),
        );

        $this->assertThrows(
            test: fn () => $this->factory
                ->editDiscount($product2, $discount) // want to edit discount to wrong product
                ->process($editDto),
            expectedClass: DiscountIsNotRelatedToProductException::class,
        );
    }

    public function test_cannot_edit_discount_with_wrong_dates(): void
    {
        $product = Product::factory()->withoutAttributes();

        $manager = Manager::factory()->superManager()->create();

        (new ProductDiscountServiceFactory($this->app))
            ->addDiscount($product)
            ->process(
                new ProductDiscountDto(
                    percentage: 25,
                    endAt: Carbon::now()->addDays(2),
                    startFrom: Carbon::now()->addDay(),
                ),
            );

        $discount = Discount::query()->where('discountable_id', $product->id)
            ->first(['id']);

        $response1 = $this
            ->actingAs($manager, RolesEnum::MANAGER->toString())
            ->putJson("api/management/warehouse/products/$product->id/discounts/$discount->id", [
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

        $response2 = $this->putJson("api/management/warehouse/products/$product->id/discounts/$discount->id", [
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

    private function createDiscount(Product $product, ProductDiscountDto $dto): Discount
    {
        $this->factory
            ->addDiscount($product)
            ->process($dto);

        return Discount::query()->where('discountable_id', $dto->variationId ?? $product->id)->first();
    }
}
