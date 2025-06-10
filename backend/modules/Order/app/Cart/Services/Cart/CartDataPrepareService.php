<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services\Cart;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Order\Cart\Dto\CartProductsQuantityDto;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Dto\UserCartInfoDto;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use RuntimeException;

class CartDataPrepareService
{
    public function __construct(
        protected WarehouseProductInfoService $warehouseService,
        protected WarehouseStockService $stockService,
        protected DiscountCartService $discountCalculator,
    ) {}

    public function prepareCartInfoForProductWithVariations(ProductCartDto $dto): UserCartInfoDto
    {
        if ($dto->variationId === null) {
            throw new RuntimeException('Variation id must be set.');
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
            throw new ProductCannotBeAddedToCartException;
        }

        $currentVariation = $product->getCurrentVariationRelation();

        return UserCartInfoDto::fromArray([
            'product_id' => $dto->product->id,
            'product_image' => $dto->product->preview_image,
            'product_title' => $dto->product->title,
            'product_slug' => $dto->product->slug,
            'quantity' => $dto->quantity,
            'price_per_unit' => $currentVariation?->price,
            'final_price' => $this->discountCalculator->countFinalPrice($currentVariation?->price, $dto->quantity),
            'discount' => $this->discountCalculator->loadDiscountIfExists($product, $dto->quantity),
            'variation' => [
                'id' => $dto->variationId,
                'data' => $currentVariation instanceof ProductVariation
                    ? $currentVariation->productAttributes->map(fn ($item) => [
                        'attribute_name' => $item->name,
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
        ]);
    }

    public function prepareCartInfoForNonVariationProduct(ProductCartDto $dto): UserCartInfoDto
    {
        if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($dto->product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException;
        }

        return UserCartInfoDto::fromArray([
            'product_id' => $dto->product->id,
            'product_image' => $dto->product->preview_image,
            'product_title' => $dto->product->title,
            'product_slug' => $dto->product->slug,
            'quantity' => $dto->quantity,
            'price_per_unit' => $dto->product->warehouse->default_price,
            'variation' => null,
            'final_price' => $this->discountCalculator->countFinalPrice(
                $dto->product->warehouse->default_price,
                $dto->quantity,
            ),
            'discount' => $this->discountCalculator->loadDiscountIfExists($dto->product, $dto->quantity),
        ]);
    }

    public function prepareCartItemsBeforeUpdate(CartProductsQuantityDto $dto): Collection
    {
        $cartItems = $dto->cartItems;

        $preparedProducts = $this->warehouseService->getWarehouseInfoAboutProducts(
            products: new EloquentCollection($cartItems->pluck('product')),
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'quantity'], []],
                combinedAttrCols: [['id', 'quantity'], []],
            ),
        )->keyBy('id');

        return $cartItems->map(fn ($item) => [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'product' => $preparedProducts->get($item->product->id, $item->product),
            'variation' => $item->variation,
        ]);
    }
}
