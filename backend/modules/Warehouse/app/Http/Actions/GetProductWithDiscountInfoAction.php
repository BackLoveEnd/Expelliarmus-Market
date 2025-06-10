<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Modules\Product\Models\Product;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;

class GetProductWithDiscountInfoAction
{
    public function __construct(
        private WarehouseProductInfoService $warehouseService,
    ) {}

    public function handle(int $productId): Product
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
