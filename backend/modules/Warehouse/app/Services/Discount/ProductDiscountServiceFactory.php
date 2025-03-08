<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Illuminate\Foundation\Application;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto;
use Modules\Warehouse\Models\Discount;

class ProductDiscountServiceFactory
{
    public function __construct(
        private Application $app,
    ) {}

    public function addDiscount(Product $product, ProductDiscountDto $dto): void
    {
        /**@var AddDiscountService $service */
        $service = $this->app->make(AddDiscountService::class, ['product' => $product]);

        $service->process($dto);
    }

    public function editDiscount(Product $product, Discount $discount, ProductDiscountDto $dto): void
    {
        /**@var EditDiscountService $service */
        $service = $this->app->make(EditDiscountService::class, [
            'product' => $product,
            'discount' => $discount,
        ]);

        $service->process($dto);
    }

    public function cancelDiscount(Product $product, Discount $discount): void
    {
        /**@var CancelDiscountService $service */
        $service = $this->app->make(CancelDiscountService::class, [
            'product' => $product,
            'discount' => $discount,
        ]);

        $service->process();
    }
}