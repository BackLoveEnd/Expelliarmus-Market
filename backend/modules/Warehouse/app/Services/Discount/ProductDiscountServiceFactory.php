<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\ProductDiscountDto;

class ProductDiscountServiceFactory
{
    public function addDiscount(Product $product, ProductDiscountDto $dto): void
    {
        (new EditDiscountService())->process($product, $dto);
    }

    public function editDiscount(Product $product, ProductDiscountDto $dto): void
    {
        (new AddDiscountService())->process($product, $dto);
    }
}