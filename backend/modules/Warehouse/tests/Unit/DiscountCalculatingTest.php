<?php

declare(strict_types=1);

namespace Modules\Warehouse\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Services\Discount\AddDiscountService;
use ReflectionClass;

class DiscountCalculatingTest extends TestCase
{
    public function test_discount_calculation_is_ok(): void
    {
        $class = new ReflectionClass(AddDiscountService::class);

        $method = $class->getMethod('calculateDiscountPrice');

        $method->setAccessible(true);

        $dto = new ProductDiscountDto(
            percentage: 30,
            endAt: Carbon::now(),
            startFrom: Carbon::now(),
        );

        $result = $method->invokeArgs(new AddDiscountService(
            Product::factory()->withoutAttributes(),
        ), [150, $dto]);

        $this->assertEquals(105.0, $result);
    }
}
