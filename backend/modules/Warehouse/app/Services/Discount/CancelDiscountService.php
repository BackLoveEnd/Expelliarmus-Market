<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Models\Discount;

class CancelDiscountService extends AbstractDiscountService
{
    public function __construct(Product $product, private Discount $discount)
    {
        parent::__construct($product);
    }

    /**
     * @throws DiscountIsNotRelatedToProductException
     */
    public function process(): void
    {
        $this->ensureDiscountRelatedToProduct($this->discount);

        $this->discount->cancelDiscount();
    }
}
