<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Modules\Product\Http\Management\Exceptions\FailedToCreateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\Warehouse;
use Throwable;

class CreateProductInWarehouse
{
    /**
     * @throws FailedToCreateProductException
     */
    public function handle(Product $product, CreateWarehouseDto $dto): Warehouse
    {
        try {
            return Warehouse::query()->create([
                'product_id' => $product->id,
                'total_quantity' => $dto->getTotalQuantity(),
                'default_price' => $this->price($dto),
                'status' => $this->getStatus($dto),
            ]);
        } catch (Throwable $e) {
            throw new FailedToCreateProductException($e->getMessage(), $e);
        }
    }

    private function price(CreateWarehouseDto $dto): ?float
    {
        if ($dto->getPrice() === null) {
            return null;
        }

        return $dto->getVariationPrices()?->filter(fn (?int $price) => $price !== null)->isEmpty()
            ? round($dto->getPrice(), 2)
            : null;
    }

    private function getStatus(CreateWarehouseDto $dto): WarehouseProductStatusEnum
    {
        $prices = $dto->getVariationPrices();

        if ($prices === null) {
            return $dto->getPrice() > 0
                ? WarehouseProductStatusEnum::IN_STOCK
                : WarehouseProductStatusEnum::PENDING;
        }

        if ($prices->filter(fn (?int $price) => $price === null)->isNotEmpty()) {
            return WarehouseProductStatusEnum::PENDING;
        }

        return WarehouseProductStatusEnum::IN_STOCK;
    }
}
