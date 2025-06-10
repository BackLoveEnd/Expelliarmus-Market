<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Exceptions\FailedToCreateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\AttributesForSingleValueDto;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\Warehouse;
use Throwable;

class CreateProductWithSingleAttributesAction implements CreateProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
        private CreateProductAttributeSingleVariationDto $singleVariationDto,
    ) {}

    public function handle(CreateProduct $createProduct, CreateProductInWarehouse $createInWarehouse): Product
    {
        return DB::transaction(function () use ($createProduct, $createInWarehouse) {
            $product = $createProduct->handle($this->productDto);

            $this->prepareWarehouseData();

            $warehouse = $createInWarehouse->handle($product, $this->warehouseDto);

            $this->handleSingleAttributeCreation($product, $warehouse);

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

    private function handleSingleAttributeCreation(Product $product, Warehouse $warehouse): void
    {
        try {
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
            throw new FailedToCreateProductException($e->getMessage(), $e);
        }
    }
}
