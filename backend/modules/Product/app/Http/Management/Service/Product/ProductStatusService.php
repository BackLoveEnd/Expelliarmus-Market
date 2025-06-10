<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Product;

use Exception;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class ProductStatusService
{
    /**
     * @throws Exception
     */
    public function publish(Product $product): void
    {
        ProductStatusEnum::checkConsistency($product->status, ProductStatusEnum::PUBLISHED);

        $product->publish();
    }

    /**
     * @throws Exception
     */
    public function unPublish(Product $product): void
    {
        ProductStatusEnum::checkConsistency($product->status, ProductStatusEnum::NOT_PUBLISHED);

        $product->unPublish();
    }
}
