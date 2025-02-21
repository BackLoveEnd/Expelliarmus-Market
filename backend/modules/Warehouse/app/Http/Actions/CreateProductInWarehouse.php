<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Modules\Product\Http\Management\Exceptions\FailedToCreateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\CreateWarehouseDto;
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
                'default_price' => $this->price($dto)
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
        return $dto->getVariationPrices()?->filter(fn(?int $price) => $price !== null)->isEmpty()
            ? round($dto->getPrice(), 2)
            : null;
    }
}