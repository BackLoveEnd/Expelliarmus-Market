<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Models\Discount;

class ProductDiscountServiceFactory
{
    public function addDiscount(Product $product, ProductDiscountDto $dto): void
    {
        (new AddDiscountService($product))->process($dto);
    }

    public function editDiscount(Product $product, Discount $discount, ProductDiscountDto $dto): void
    {
        (new EditDiscountService($product, $discount))->process($dto);
    }
}