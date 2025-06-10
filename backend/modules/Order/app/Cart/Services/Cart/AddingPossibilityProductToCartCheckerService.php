<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services\Cart;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Exceptions\HasNotEnoughSuppliesForUpdateException;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;

class AddingPossibilityProductToCartCheckerService
{
    public function __construct(protected WarehouseStockService $stockService) {}

    public function ensureProductCanBeAddedToCart(Product $product): void
    {
        if (! $product->status->is(ProductStatusEnum::PUBLISHED)) {
            throw new ProductCannotBeAddedToCartException;
        }

        if (! $this->stockService->isPartiallyOrFullyInStock($product)) {
            throw new ProductCannotBeAddedToCartException;
        }
    }

    public function ensureCanUpdateProductsQuantity(Collection $cartItems): void
    {
        $cartItems->each(function (array $item) {
            /** @var Product $product */
            $product = $item['product'];

            if ($product->hasCombinedAttributes()) {
                $product->combinedAttributes = $product->combinedAttributes->firstWhere('id', $item['variation']);
            } elseif ($product->hasCombinedAttributes() === false) {
                $product->singleAttributes = $product->singleAttributes->firstWhere('id', $item['variation']);
            }

            if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($product, $item['quantity'])) {
                throw HasNotEnoughSuppliesForUpdateException::fromProductArticle($product->product_article);
            }
        });
    }
}
