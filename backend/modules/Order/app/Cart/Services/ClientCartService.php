<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services;

use Illuminate\Contracts\Session\Session;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Models\Product;
use Modules\User\Contracts\UserInterface;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;

class ClientCartService
{
    public function __construct(
        protected Session $session,
        protected WarehouseProductInfoService $warehouseService,
        protected WarehouseStockService $stockService,
    ) {}

    public function getCart(?UserInterface $user) {}

    public function addToCart(?UserInterface $user, ProductCartDto $dto)
    {
        $product = $dto->product;

        $this->ensureProductCanBeAddedToCart($product);

        if (is_null($product->hasCombinedAttributes())) {
            $this->addProductWithoutVariationToCart($dto);
        }

        $this->addProductWithVariationsToCart($dto);
    }

    protected function ensureProductCanBeAddedToCart(Product $product): void
    {
        if (! $product->status->is(ProductStatusEnum::PUBLISHED)) {
            throw new ProductCannotBeAddedToCartException();
        }

        if (! $this->stockService->isPartiallyOrFullyInStock($product)) {
            throw new ProductCannotBeAddedToCartException();
        }
    }

    private function addProductWithVariationsToCart(ProductCartDto $dto)
    {
        $product = $this->warehouseService->getProductAttributeById(
            product: $dto->product,
            variationId: $dto->variationId,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price', 'quantity'], []],
                combinedAttrCols: [['id', 'price', 'quantity'], []],
            ),
        );

        if ($this->stockService->hasEnoughSuppliesForRequestedQuantity($product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }
    }

    private function addProductWithoutVariationToCart(ProductCartDto $dto)
    {
        if ($this->stockService->hasEnoughSuppliesForRequestedQuantity($dto->product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }
    }
}