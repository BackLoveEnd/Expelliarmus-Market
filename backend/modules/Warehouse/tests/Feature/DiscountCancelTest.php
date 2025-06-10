<?php

declare(strict_types=1);

namespace Modules\Warehouse\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Services\Discount\ProductDiscountServiceFactory;

class DiscountCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_cancel_discount(): void
    {
        $product = Product::factory()->withoutAttributes();

        $service = new ProductDiscountServiceFactory($this->app);

        $service->addDiscount($product)->process(
            new ProductDiscountDto(
                percentage: 25,
                endAt: Carbon::now()->addDays(2),
                startFrom: Carbon::now()->addDay(),
            ),
        );

        $discount = Discount::query()->where('discountable_id', $product->id)
            ->first();

        (new ProductDiscountServiceFactory($this->app))
            ->cancelDiscount($product, $discount)
            ->process();

        $this->assertDatabaseHas('discounts', [
            'discountable_id' => $product->id,
            'status' => DiscountStatusEnum::CANCELLED,
        ]);
    }
}
