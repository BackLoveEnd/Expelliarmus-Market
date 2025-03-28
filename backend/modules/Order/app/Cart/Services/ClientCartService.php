<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services;

use Illuminate\Contracts\Session\Session;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;
use Modules\User\Contracts\UserInterface;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use RuntimeException;

class ClientCartService
{
    public function __construct(
        protected Session $session,
        protected WarehouseProductInfoService $warehouseService,
        protected WarehouseStockService $stockService,
        protected DiscountedProductsService $discountService,
    ) {}

    public function getCart(?UserInterface $user) {}

    public function addToCart(?UserInterface $user, ProductCartDto $dto)
    {
        $product = $dto->product;

        $this->ensureProductCanBeAddedToCart($product);

        if (is_null($product->hasCombinedAttributes())) {
            $cartInfo = $this->prepareCartInfoForNonVariationProduct($dto);
        }

        $cartInfo = $this->prepareCartInfoForProductWithVariations($dto);
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

    protected function countFinalPrice(float $price, int $quantity): float
    {
        return round($price * $quantity, 2);
    }

    private function prepareCartInfoForProductWithVariations(ProductCartDto $dto)
    {
        if ($dto->variationId === null) {
            throw new RuntimeException("Variation id must be set. ".__CLASS__);
        }

        $product = $this->warehouseService->getProductAttributeById(
            product: $dto->product,
            variationId: $dto->variationId,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price', 'quantity', 'value', 'attribute_id'], ['id', 'name', 'type']],
                combinedAttrCols: [['id', 'price', 'quantity'], ['name', 'type']],
            ),
        );

        if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }

        $product = $this->discountService->loadLastActiveDiscountForProduct($product);

        $currentVariation = $product->getCurrentVariationRelation();

        $cartInfo = [
            'product_id' => $dto->product->id,
            'quantity' => $dto->quantity,
            'warehouse' => $currentVariation?->price,
            'final_price' => $this->countFinalPrice($currentVariation?->price, $dto->quantity),
            'discount' => null,
            'variation' => [
                'id' => $dto->variationId,
                'data' => $currentVariation instanceof ProductVariation
                    ? $currentVariation->productAttributes->map(fn($item)
                        => [
                        'name' => $item->name,
                        'value' => $item->pivot->value,
                        'type' => $item->type->toTypes(),
                    ])
                    : [
                        [
                            'value' => $currentVariation->value,
                            'attribute_type' => $currentVariation->attribute->type->toTypes(),
                            'attribute_name' => $currentVariation->attribute->name,
                        ],
                    ],
            ],
        ];

        if ($this->discountService->productHasActiveDiscount($product)) {
            $cartInfo['discount'] = [
                'id' => $product->lastActiveDiscount->id,
                'percentage' => $product->lastActiveDiscount->percentage,
                'new_price' => $product->lastActiveDiscount->discount_price,
                'end_date' => $product->lastActiveDiscount->end_date,
            ];
        }

        return $cartInfo;
    }

    private function prepareCartInfoForNonVariationProduct(ProductCartDto $dto): array
    {
        if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($dto->product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }

        $product = $this->discountService->loadLastActiveDiscountForProduct($dto->product);

        $cartInfo = [
            'product_id' => $dto->product->id,
            'quantity' => $dto->quantity,
            'warehouse' => $product->warehouse->default_price,
            'variation' => null,
            'final_price' => $this->countFinalPrice($product->warehouse->defaultPrice, $dto->quantity),
            'discount' => null,
        ];

        if ($this->discountService->productHasActiveDiscount($product)) {
            $cartInfo['discount'] = [
                'id' => $product->lastActiveDiscount->id,
                'percentage' => $product->lastActiveDiscount->percentage,
                'new_price' => $product->lastActiveDiscount->discount_price,
                'end_date' => $product->lastActiveDiscount->end_date,
            ];
        }

        return $cartInfo;
    }
}