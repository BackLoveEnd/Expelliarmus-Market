<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Warehouse;

use App\Helpers\ArithmeticOperator as Operator;
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

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     */
    public function decreaseProductsStock(Collection $productsWithQuantities): void
    {
        [$productsWithoutVariation, $productsWithVariation] = $productsWithQuantities->partition(
            fn (array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === null,
        );

        [$productsWithSingleVar, $productsWithCombinedVar] = $productsWithVariation->partition(
            fn (array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === false,
        );

        $this->adjustQuantityForProductsWithoutVariations($productsWithoutVariation, Operator::MINUS);

        $this->adjustQuantityForProductsWithSingleVariation($productsWithSingleVar, Operator::MINUS);

        $this->adjustQuantityForProductsWithCombinedVariation($productsWithCombinedVar, Operator::MINUS);
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     */
    public function returnReservedProductsToStock(Collection $productsWithQuantities): void
    {
        [$productsWithoutVariation, $productsWithVariation] = $productsWithQuantities->partition(
            fn (array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === null,
        );

        [$productsWithSingleVar, $productsWithCombinedVar] = $productsWithVariation->partition(
            fn (array $productWithQuantity) => $productWithQuantity['product']->hasCombinedAttributes() === false,
        );

        $this->adjustQuantityForProductsWithoutVariations($productsWithoutVariation, Operator::PLUS);

        $this->adjustQuantityForProductsWithSingleVariation($productsWithSingleVar, Operator::PLUS);

        $this->adjustQuantityForProductsWithCombinedVariation($productsWithCombinedVar, Operator::PLUS);
    }

    /**
     * @param  Collection<int, array{product: Product, quantity: int}>  $productsWithQuantities
     */
    public function adjustQuantityForProductsWithoutVariations(
        Collection $productsWithQuantities,
        Operator $operator,
    ): void {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) use ($operator) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                if ($operator === Operator::PLUS) {
                    $newQuantity = $product->warehouse->total_quantity + $productWithQuantity['quantity'];
                } elseif ($operator === Operator::MINUS) {
                    $newQuantity = $product->warehouse->total_quantity - $productWithQuantity['quantity'];
                } else {
                    $newQuantity = $product->warehouse->total_quantity;
                }

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
     */
    public function adjustQuantityForProductsWithSingleVariation(
        Collection $productsWithQuantities,
        Operator $operator,
    ): void {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) use ($operator) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                if ($operator === Operator::PLUS) {
                    $newQuantity = $product->singleAttributes->quantity + $productWithQuantity['quantity'];
                } elseif ($operator === Operator::MINUS) {
                    $newQuantity = $product->singleAttributes->quantity - $productWithQuantity['quantity'];
                } else {
                    $newQuantity = $product->singleAttributes->quantity;
                }

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
     */
    public function adjustQuantityForProductsWithCombinedVariation(
        Collection $productsWithQuantities,
        Operator $operator,
    ): void {
        if ($productsWithQuantities->isNotEmpty()) {
            $cases = $productsWithQuantities->map(function (array $productWithQuantity) use ($operator) {
                /** @var Product $product */
                $product = $productWithQuantity['product'];

                if ($operator === Operator::PLUS) {
                    $newQuantity = $product->combinedAttributes->quantity + $productWithQuantity['quantity'];
                } elseif ($operator === Operator::MINUS) {
                    $newQuantity = $product->combinedAttributes->quantity - $productWithQuantity['quantity'];
                } else {
                    $newQuantity = $product->combinedAttributes->quantity;
                }

                return "WHEN id = {$product->combinedAttributes->id} THEN $newQuantity";
            })->implode(' ');

            ProductVariation::query()
                ->whereIn('id', $productsWithQuantities->pluck('product.combinedAttributes.id'))
                ->update([
                    'quantity' => DB::raw("CASE $cases ELSE quantity END"),
                ]);
        }
    }

    protected function ensureVariationRelationProvided(Product $product, string $neededVariationRelation): void
    {
        if (! $product->relationLoaded($neededVariationRelation)
            || $product->$neededVariationRelation->quantity === null
        ) {
            throw new RuntimeException(
                'Relation for product with quantity must be loaded in order to get supplies quantity info. '.__CLASS__,
            );
        }
    }
}
