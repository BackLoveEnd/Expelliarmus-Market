<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Exceptions\FailedToUpdateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeCombinedVariationsDto as CombinedVariationsDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;
use Modules\Warehouse\Models\VariationAttributeValues;
use Modules\Warehouse\Models\Warehouse;
use Throwable;

class EditProductWithCombinedOptions implements EditProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
        /** @var Collection<int, CombinedVariationsDto> */
        private Collection $combinedVariationsDto,
    ) {}

    public function handle(EditProduct $editProduct, EditProductInWarehouse $editProductInWarehouse): Product
    {
        return DB::transaction(function () use ($editProduct, $editProductInWarehouse) {
            $product = $editProduct->handle($this->productDto);

            $this->prepareWarehouseData();

            $warehouse = $editProductInWarehouse->handle($this->warehouseDto);

            $this->updateVariations($product, $warehouse);

            return $product;
        });
    }

    private function prepareWarehouseData(): void
    {
        $this->warehouseDto->setTotalQuantity(
            $this->combinedVariationsDto->sum('quantity'),
        );

        $this->warehouseDto->setVariationPrices(
            $this->combinedVariationsDto->pluck('price'),
        );
    }

    private function updateVariations(Product $product, Warehouse $warehouse): void
    {
        try {
            $this->updateProductVariation($product, $warehouse);

            $this->updateVariationsValues($product);
        } catch (Throwable $e) {
            throw new FailedToUpdateProductException($e->getMessage());
        }
    }

    private function updateProductVariation(Product $product, Warehouse $warehouse): void
    {
        $currentVariations = $product->combinedAttributes->pluck('sku');

        $mappedVariations = $this->combinedVariationsDto->map(function ($dto) use ($product, $warehouse) {
            return [
                'sku' => $dto->skuName,
                'quantity' => $dto->quantity,
                'price' => $dto->price
                    ? round($dto->price, 2)
                    : $warehouse->default_price,
                'product_id' => $product->id,
            ];
        });

        $toDelete = $currentVariations->diff($mappedVariations->pluck('sku'));

        if ($toDelete->isNotEmpty()) {
            $product
                ->combinedAttributes()->whereIn('sku', $toDelete->toArray())
                ->delete();
        }

        $product->combinedAttributes()->upsert(
            values: $mappedVariations->toArray(),
            uniqueBy: ['sku'],
            update: ['sku', 'price', 'quantity'],
        );
    }

    private function updateVariationsValues(Product $product): void
    {
        $product->load('combinedAttributes');

        $variations = $product->combinedAttributes->keyBy('sku');

        $attributesToUpsert = $this->combinedVariationsDto->flatMap(function ($dto) use ($variations) {
            $variation = $variations[$dto->skuName] ?? null;
            if (! $variation) {
                return [];
            }

            return collect($dto->attributes)->map(function ($attribute) use ($variation) {
                return [
                    'variation_id' => $variation->id,
                    'attribute_id' => $attribute->id,
                    'value' => $attribute->value,
                ];
            });
        });

        VariationAttributeValues::query()->upsert(
            values: $attributesToUpsert->toArray(),
            uniqueBy: ['variation_id', 'attribute_id'],
            update: ['value'],
        );
    }
}
