<?php

namespace Modules\Warehouse\Services\Discount;

use Modules\Warehouse\DTO\Discount\ProductDiscountDto;

interface DiscountProcessingInterface
{
    public function process(ProductDiscountDto $dto): void;
}
