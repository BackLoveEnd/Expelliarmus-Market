<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Illuminate\Foundation\Application;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\Discount;

class ProductDiscountServiceFactory
{
    public function __construct(
        private Application $app,
    ) {}

    public function addDiscount(Product $product): DiscountProcessingInterface
    {
        return $this->app->make(AddDiscountService::class, ['product' => $product]);
    }

    public function editDiscount(Product $product, Discount $discount): DiscountProcessingInterface
    {
        return $this->app->make(EditDiscountService::class, [
            'product' => $product,
            'discount' => $discount,
        ]);
    }

    public function cancelDiscount(Product $product, Discount $discount): CancelDiscountService
    {
        return $this->app->make(CancelDiscountService::class, [
            'product' => $product,
            'discount' => $discount,
        ]);
    }
}
