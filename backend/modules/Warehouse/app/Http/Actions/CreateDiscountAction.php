<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Models\Discount;

class CreateDiscountAction
{
    public function handle(ProductDiscountDto $dto, float $oldPrice, float $newPrice)
    {
        return Discount::query()->create([
            'percentage' => $dto->percentage,
            'original_price' => $oldPrice,
            'discount_price' => $newPrice,
            'start_date' => $dto->startFrom,
            'end_date' => $dto->endAt,
        ]);
    }
}
