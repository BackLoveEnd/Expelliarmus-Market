<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\ProductDiscountDto as DiscountDto;

class EditDiscountService extends AbstractDiscountService
{
    public function process(Product $product, DiscountDto $dto): void {}
}