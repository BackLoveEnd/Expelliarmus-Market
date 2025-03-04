<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services;

use Modules\Product\Models\Product;

class ProductDiscountService
{
    public function __construct(
        protected WarehouseProductInfoService $warehouseService,
    ) {}

    public function getProductWithDiscounts(int $productId): Product
    {
        $product = $this->warehouseService->getWarehouseInfoAboutProduct($productId);

        if (is_null($product->hasCombinedAttributes())) {
            $product->load('lastDiscount');

            return $product;
        }

        $product->productAttributes->load('lastDiscount');

        return $product;
    }
}