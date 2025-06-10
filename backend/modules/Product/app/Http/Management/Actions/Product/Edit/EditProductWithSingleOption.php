<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Exceptions\FailedToUpdateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\AttributesForSingleValueDto;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\Warehouse;
use Throwable;

class EditProductWithSingleOption implements EditProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
        private CreateProductAttributeSingleVariationDto $singleVariationDto,
    ) {}

    public function handle(EditProduct $editProduct, EditProductInWarehouse $editProductInWarehouse): Product
    {
        return DB::transaction(function () use ($editProduct, $editProductInWarehouse) {
            $product = $editProduct->handle($this->productDto);

            $this->prepareWarehouseData();

            $warehouse = $editProductInWarehouse->handle($this->warehouseDto);

            $this->updateSingleVariation($product, $warehouse);

            return $product;
        });
    }

    private function prepareWarehouseData(): void
    {
        $this->warehouseDto->setTotalQuantity(
            $this->singleVariationDto->attributes->sum('quantity'),
        );

        $this->warehouseDto->setVariationPrices(
            $this->singleVariationDto->attributes->pluck('price'),
        );
    }

    private function updateSingleVariation(Product $product, Warehouse $warehouse): void
    {
        try {
            $product->singleAttributes()->delete();

            $attributes = $this->singleVariationDto->attributes->map(
                function (AttributesForSingleValueDto $dto) use ($product, $warehouse) {
                    return [
                        'product_id' => $product->id,
                        'attribute_id' => $this->singleVariationDto->attributeId,
                        'value' => $dto->value,
                        'quantity' => $dto->quantity,
                        'price' => $dto->price ? round($dto->price, 2) : $warehouse->default_price,
                    ];
                },
            );

            ProductAttributeValue::query()->insert($attributes->toArray());
        } catch (Throwable $e) {
            throw new FailedToUpdateProductException($e->getMessage());
        }
    }
}
