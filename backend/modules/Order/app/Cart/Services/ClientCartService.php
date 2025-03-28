<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Dto\UserCartInfoDto;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
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

    public function getCart(?User $user): array
    {
        if ($this->session->has('user.cart')) {
            return $this->session->get('user.cart');
        }

        if ($user) {
            $cart = $user->cart()->toBase()->get();

            if ($cart->isNotEmpty()) {
                $this->session->put('user.cart', $cart->toArray());
            }
        }

        return $this->session->get('user.cart', []);
    }

    /**
     * @param  User|null  $user
     * @param  ProductCartDto  $dto
     * @return void
     * @throws ProductCannotBeAddedToCartException
     */
    public function addToCart(?User $user, ProductCartDto $dto): void
    {
        $this->ensureProductCanBeAddedToCart($dto->product);

        if (is_null($dto->product->hasCombinedAttributes())) {
            $cartInfo = $this->prepareCartInfoForNonVariationProduct($dto);

            $this->saveCartForUser($user, $cartInfo);

            return;
        }

        $cartInfo = $this->prepareCartInfoForProductWithVariations($dto);

        $this->saveCartForUser($user, $cartInfo);
    }

    public function removeFromCart(Product $product) {}

    public function clearCart(?User $user): void
    {
        $user?->cart()->delete();

        $this->session->forget('user.cart');
    }

    public function isCartEmpty(?User $user): bool
    {
        if ($user) {
            if ($user->cart()->exists()) {
                return false;
            }

            return true;
        }

        return ! $this->session->has('user.cart');
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

    protected function saveCartForUser(?User $user, UserCartInfoDto $cart): void
    {
        if (! $user) {
            $this->session->push('user.cart', $cart->toBase());

            return;
        }

        DB::transaction(function () use ($user, $cart) {
            $user->cart()->create($cart->toArray());

            $this->session->push('user.cart', $cart->toBase());
        });
    }

    protected function loadDiscountIfExists(Product $product, int $requestedQuantity): ?array
    {
        $product = $this->discountService->loadLastActiveDiscountForProduct($product);

        if ($this->discountService->productHasActiveDiscount($product)) {
            $cartInfo['discount'] = [
                'id' => $product->lastActiveDiscount->id,
                'percentage' => $product->lastActiveDiscount->percentage,
                'new_price' => $product->lastActiveDiscount->discount_price,
                'final_price' => $this->countFinalPrice(
                    $product->lastActiveDiscount->discount_price,
                    $requestedQuantity,
                ),
                'end_date' => $product->lastActiveDiscount->end_date,
            ];

            return $cartInfo;
        }

        return null;
    }

    private function prepareCartInfoForProductWithVariations(ProductCartDto $dto): UserCartInfoDto
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

        $currentVariation = $product->getCurrentVariationRelation();

        return UserCartInfoDto::fromArray([
            'product_id' => $dto->product->id,
            'quantity' => $dto->quantity,
            'price_per_unit' => $currentVariation?->price,
            'final_price' => $this->countFinalPrice($currentVariation?->price, $dto->quantity),
            'discount' => $this->loadDiscountIfExists($product, $dto->quantity),
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
        ]);
    }

    private function prepareCartInfoForNonVariationProduct(ProductCartDto $dto): UserCartInfoDto
    {
        if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($dto->product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }

        return UserCartInfoDto::fromArray([
            'product_id' => $dto->product->id,
            'quantity' => $dto->quantity,
            'price_per_unit' => $dto->product->warehouse->default_price,
            'variation' => null,
            'final_price' => $this->countFinalPrice($dto->product->warehouse->default_price, $dto->quantity),
            'discount' => $this->loadDiscountIfExists($dto->product, $dto->quantity),
        ]);
    }
}