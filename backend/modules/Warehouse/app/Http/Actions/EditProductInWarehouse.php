<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\CreateWarehouseDto;
use Modules\Warehouse\Models\Warehouse;

class EditProductInWarehouse
{
    public function __construct(private Product $product)
    {
    }

    public function handle(CreateWarehouseDto $warehouseDto): Warehouse
    {
        $this->product->load('warehouse');

        $this->product->warehouse->update([
            'default_price' => $this->price($warehouseDto),
            'total_quantity' => $warehouseDto->getTotalQuantity()
        ]);

        return $this->product->warehouse;
    }

    private function price(CreateWarehouseDto $dto): ?float
    {
        if ($dto->getPrice() === null) {
            return null;
        }

        if ($dto->getVariationPrices() === null) {
            return $dto->getPrice();
        }

        return $dto->getVariationPrices()->filter(fn(?int $price) => $price !== null)->isEmpty()
            ? round($dto->getPrice(), 2)
            : null;
    }
}