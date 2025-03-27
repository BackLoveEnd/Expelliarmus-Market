<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Warehouse;

use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
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
}