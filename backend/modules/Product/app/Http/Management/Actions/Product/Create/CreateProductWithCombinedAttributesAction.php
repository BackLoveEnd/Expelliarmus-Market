<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Exceptions\FailedToCreateProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\AttributesForCombinedValueDto;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeCombinedVariationsDto as CombinedVariationsDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Models\VariationAttributeValues;
use Modules\Warehouse\Models\Warehouse;
use Throwable;

class CreateProductWithCombinedAttributesAction implements CreateProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
        /** @var Collection<int, CombinedVariationsDto> */
        private Collection $combinedVariationsDto,
    ) {}

    public function handle(CreateProduct $createProduct, CreateProductInWarehouse $createInWarehouse): Product
    {
        return DB::transaction(function () use ($createProduct, $createInWarehouse) {
            $product = $createProduct->handle($this->productDto);

            $this->prepareWarehouseData();

            $warehouse = $createInWarehouse->handle($product, $this->warehouseDto);

            $this->handleCreatingCombinedAttributes($product, $warehouse);

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

    /**
     * @throws FailedToCreateProductException
     */
    private function handleCreatingCombinedAttributes(Product $product, Warehouse $warehouse): void
    {
        try {
            $productVariations = $this->prepareProductVariations($product, $warehouse);

            $this->linkValuesToVariationAttributes($productVariations);
        } catch (Throwable $e) {
            throw new FailedToCreateProductException($e->getMessage(), $e);
        }
    }

    private function prepareProductVariations(Product $product, Warehouse $warehouse): EloquentCollection
    {
        $productVariations = $this->combinedVariationsDto->map(
            function (CombinedVariationsDto $dto) use ($product, $warehouse) {
                return [
                    'product_id' => $product->id,
                    'sku' => $dto->skuName,
                    'quantity' => $dto->quantity,
                    'price' => $dto->price
                        ? round($dto->price, 2)
                        : $warehouse->default_price,
                ];
            },
        );

        return $this->createVariations($productVariations->toArray(), $product);
    }

    private function createVariations(array $productVariations, Product $product): EloquentCollection
    {
        ProductVariation::query()->insert($productVariations);

        return ProductVariation::query()->where('product_id', $product->id)
            ->get(['id']);
    }

    private function linkValuesToVariationAttributes(EloquentCollection $productVariations): void
    {
        $variationAttributes = $productVariations->map(function (ProductVariation $variation, int $index) {
            $dto = $this->combinedVariationsDto[$index];

            return $dto->attributes->map(
                function (AttributesForCombinedValueDto $attribute) use ($variation) {
                    return [
                        'variation_id' => $variation->id,
                        'attribute_id' => $attribute->id,
                        'value' => $attribute->value,
                    ];
                },
            );
        });

        VariationAttributeValues::query()->insert($variationAttributes->collapse()->toArray());
    }
}
