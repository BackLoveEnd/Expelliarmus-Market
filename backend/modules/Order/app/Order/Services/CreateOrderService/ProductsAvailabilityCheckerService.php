<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Order\Order\Exceptions\ProductCannotBeProcessedToCheckoutException;
use Modules\Order\Order\Exceptions\ProductHasNotEnoughSuppliesException;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;

class ProductsAvailabilityCheckerService
{
    public function __construct(
        private WarehouseStockService $stockService,
        private WarehouseProductInfoService $warehouseService,
    ) {}

    public function ensureProductsCanBeProcessedToCheckout(Collection $products): EloquentCollection
    {
        $products = (new EloquentCollection($products))->loadMissing('warehouse');

        $products->each(function (Product $product) {
            if (! $product->status->is(ProductStatusEnum::PUBLISHED)) {
                throw new ProductCannotBeProcessedToCheckoutException;
            }

            if (! $this->stockService->isPartiallyOrFullyInStock($product)) {
                throw new ProductCannotBeProcessedToCheckoutException;
            }
        });

        return $products;
    }

    public function ensureHasEnoughSupplies(Collection $items): Collection
    {
        $productsWithVariations = $this->warehouseService->getProductsAttributeById(
            productsWithVariationId: $items,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price', 'quantity', 'value', 'attribute_id'], ['id', 'name', 'type']],
                combinedAttrCols: [['id', 'price', 'quantity'], ['name', 'type']],
                warehouseCols: ['id', 'total_quantity', 'default_price'],
            ),
        );

        $productsWithVariations->select('product', 'quantity')->each(function (array $item) {
            if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($item['product'], $item['quantity'])) {
                throw ProductHasNotEnoughSuppliesException::fromProductArticle($item['product']->product_article);
            }
        });

        return $productsWithVariations;
    }
}
