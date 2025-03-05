<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\ProductDiscountDto as DiscountDto;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;

abstract class AbstractDiscountService
{
    protected function calculateDiscountPrice(float $originalPrice, DiscountDto $dto): float
    {
        if ((int) $originalPrice === 0) {
            throw new CannotAddDiscountToProductWithoutPriceException();
        }

        return round(
            num: $originalPrice * (1 - ($dto->percentage / 100)),
            precision: 2,
        );
    }

    abstract public function process(Product $product, DiscountDto $dto): void;
}