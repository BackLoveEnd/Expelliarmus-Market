<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Warehouse;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Models\Warehouse;
use RuntimeException;

class WarehouseStockService
{
    public function __construct(
        protected WarehouseProductInfoService $warehouseInfoService,
    ) {}

    public function isPartiallyOrFullyInStock(Product $product): bool
    {
        $product->loadMissing('warehouse');

        return $product->warehouse->status->isIn([
            WarehouseProductStatusEnum::IN_STOCK,
            WarehouseProductStatusEnum::PARTIALLY,
        ]);
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     * @return void
     */
    public function decreaseProductsStock(Collection $productsWithQuantities): void
    {
        [$productsWithoutVariation, $productsWithVariation] = $productsWithQuantities->partition(
            fn(array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === null,
        );

        [$productsWithSingleVar, $productsWithCombinedVar] = $productsWithVariation->partition(
            fn(array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === false,
        );

        $this->decreaseQuantityForProductsWithoutVariations($productsWithoutVariation);

        $this->decreaseQuantityForProductsWithSingleVariation($productsWithSingleVar);

        $this->decreaseQuantityForProductsWithCombinedVariation($productsWithCombinedVar);
    }

    public function hasEnoughSuppliesForRequestedQuantity(Product $product, int $requestedQuantity): bool
    {
        if (is_null($product->hasCombinedAttributes())) {
            return $product->warehouse->total_quantity > $requestedQuantity;
        }

        if ($product->hasCombinedAttributes()) {
            $this->ensureVariationRelationProvided($product, 'combinedAttributes');

            return $product->combinedAttributes->quantity > $requestedQuantity;
        }

        $this->ensureVariationRelationProvided($product, 'singleAttributes');

        return $product->singleAttributes->quantity > $requestedQuantity;
    }

    protected function ensureVariationRelationProvided(Product $product, string $neededVariationRelation): void
    {
        if (! $product->relationLoaded($neededVariationRelation)
            || $product->$neededVariationRelation->quantity === null
        ) {
            throw new RuntimeException(
                "Relation for product with quantity must be loaded in order to get supplies quantity info. ".__CLASS__,
            );
        }
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     * @return void
     */
    protected function decreaseQuantityForProductsWithoutVariations(Collection $productsWithQuantities): void
    {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                $newQuantity = $product->warehouse->total_quantity - $productWithQuantity['quantity'];

                return "WHEN product_id = {$product->id} THEN $newQuantity";
            })->implode(' ');

            Warehouse::query()->whereIn('product_id', $productsWithQuantities->pluck('product.id'))
                ->update([
                    'total_quantity' => DB::raw("CASE $cases ELSE total_quantity END"),
                ]);
        }
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     * @return void
     */
    public function decreaseQuantityForProductsWithSingleVariation(Collection $productsWithQuantities): void
    {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                $newQuantity = $product->singleAttributes->quantity - $productWithQuantity['quantity'];

                return "WHEN id = {$product->singleAttributes->id} THEN $newQuantity";
            })->implode(' ');

            ProductAttributeValue::query()
                ->whereIn('id', $productsWithQuantities->pluck('product.singleAttributes.id'))
                ->update([
                    'quantity' => DB::raw("CASE $cases ELSE quantity END"),
                ]);
        }
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     * @return void
     */
    public function decreaseQuantityForProductsWithCombinedVariation(Collection $productsWithQuantities): void
    {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                $newQuantity = $product->combinedAttributes->quantity - $productWithQuantity['quantity'];

                return "WHEN id = {$product->combinedAttributes->id} THEN $newQuantity";
            })->implode(' ');

            ProductVariation::query()
                ->whereIn('id', $productsWithQuantities->pluck('product.combinedAttributes.id'))
                ->update([
                    'quantity' => DB::raw("CASE $cases ELSE quantity END"),
                ]);
        }
    }
}