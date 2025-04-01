<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services;

use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Cart\Dto\CartProductsQuantityDto;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Dto\UserCartInfoDto;
use Modules\Order\Cart\Exceptions\HasNotEnoughSuppliesForUpdateException;
use Modules\Order\Cart\Exceptions\ProductCannotBeAddedToCartException;
use Modules\Order\Models\Cart;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Models\ProductVariation;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class ClientCartService
{
    protected string $cartSessionKey = 'user.cart';

    public function __construct(
        protected Session $session,
        protected WarehouseProductInfoService $warehouseService,
        protected WarehouseStockService $stockService,
        protected DiscountedProductsService $discountService,
    ) {}

    public function getCart(?User $user): array
    {
        if ($this->session->has($this->cartSessionKey)) {
            return $this->session->get($this->cartSessionKey);
        }

        if ($user) {
            $carts = $user
                ->cart()->with('product:id,preview_image,title')
                ->get();

            if ($carts->isNotEmpty()) {
                $this->session->put($this->cartSessionKey, UserCartInfoDto::fromModels($carts)->toArray());
            }
        }

        return $this->session->get($this->cartSessionKey, []);
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

    /**
     * @throws HasNotEnoughSuppliesForUpdateException
     */
    public function updateProductsQuantities(?User $user, CartProductsQuantityDto $dto): void
    {
        $cartsInSession = $this->getCart($user);

        if (! $cartsInSession) {
            return;
        }

        $updatedCartItems = $this->prepareCartItemsBeforeUpdate($dto);

        $this->ensureCanUpdateProductsQuantity($updatedCartItems);

        $updatedCart = $this->updateQuantityInSession($dto, collect($cartsInSession));

        $this->updateForAuthUser($user, $updatedCart);
    }

    public function removeFromCart(?User $user, string $id): void
    {
        $user?->cart()->delete($id);

        $userCart = $this->session->get($this->cartSessionKey);

        if ($userCart) {
            $userCart = array_values(array_filter($userCart, static function (object $cartInfo) use ($id) {
                return $cartInfo->id !== $id;
            }));

            $this->session->put($this->cartSessionKey, $userCart);
        }
    }

    public function clearCart(?User $user): void
    {
        $user?->cart()->delete();

        $this->session->forget($this->cartSessionKey);
    }

    public function isCartEmpty(?User $user): bool
    {
        return $user ? ! $user->cart()->exists() : ! $this->session->has($this->cartSessionKey);
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

    protected function updateQuantityInSession(
        CartProductsQuantityDto $dto,
        BaseCollection $cartsInSession,
    ): BaseCollection {
        $updatedCart = $cartsInSession->map(function (object $cartItem) use ($dto) {
            $matchingItem = $dto->cartItems->firstWhere('id', $cartItem->id);

            if ($matchingItem) {
                $cartItem->quantity = $matchingItem->quantity;
                $cartItem->final_price = $this->countFinalPrice($cartItem->price_per_unit, $matchingItem->quantity);
            }

            return $cartItem;
        });

        $this->session->put($updatedCart->toArray());

        return $updatedCart;
    }

    protected function updateForAuthUser(?User $user, BaseCollection $updatedCartInfo): void
    {
        if (! $user) {
            return;
        }

        DB::transaction(function () use ($user, $updatedCartInfo) {
            $cartData = $updatedCartInfo->select([
                'id',
                'product_id',
                'quantity',
                'price_per_unit',
                'final_price',
                'discount',
                'variation',
            ]);

            $cartData = $cartData->map(fn($item) => [...$item, 'user_id' => $user->id]);

            Cart::query()->where('user_id', $user->id)
                ->upsert($cartData->toArray(), 'id', ['quantity', 'final_price']);
        });
    }

    protected function countFinalPrice(float $price, int $quantity): float
    {
        return round($price * $quantity, 2);
    }

    protected function saveCartForUser(?User $user, UserCartInfoDto $cart): void
    {
        if (! $user) {
            $this->session->push($this->cartSessionKey, $cart->setId(Uuid::uuid7()->toString())->toBase());

            return;
        }

        DB::transaction(function () use ($user, $cart) {
            $cartDb = $user->cart()->create($cart->toArray());

            $this->session->push($this->cartSessionKey, $cart->setId($cartDb->id)->toBase());
        });
    }

    protected function loadDiscountIfExists(Product $product, int $requestedQuantity): ?array
    {
        $product = $this->discountService->loadLastActiveDiscountForProduct($product);

        if ($this->discountService->productHasActiveDiscount($product)) {
            $currentRelation = $product->getCurrentVariationRelation();

            $lastDiscount = $currentRelation->lastActiveDiscount->first();

            return [
                'id' => $lastDiscount->id,
                'percentage' => $lastDiscount->percentage,
                'new_price' => $lastDiscount->discount_price,
                'final_price' => $this->countFinalPrice(
                    $lastDiscount->discount_price,
                    $requestedQuantity,
                ),
                'end_date' => $lastDiscount->end_date,
            ];
        }

        return null;
    }

    protected function ensureCanUpdateProductsQuantity(BaseCollection $updatedCartItems): void
    {
        $updatedCartItems->each(function (array $item) {
            /**@var Product $product */
            $product = $item['product'];

            if ($product->hasCombinedAttributes()) {
                $product->combinedAttributes = $product->combinedAttributes->firstWhere('id', $item['variation']);
            } elseif ($product->hasCombinedAttributes() === false) {
                $product->singleAttributes = $product->singleAttributes->firstWhere('id', $item['variation']);
            }

            if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($product, $item['quantity'])) {
                throw HasNotEnoughSuppliesForUpdateException::fromProductArticle($product->product_article);
            }
        });
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
            'product_image' => $dto->product->preview_image,
            'product_title' => $dto->product->title,
            'quantity' => $dto->quantity,
            'price_per_unit' => $currentVariation?->price,
            'final_price' => $this->countFinalPrice($currentVariation?->price, $dto->quantity),
            'discount' => $this->loadDiscountIfExists($product, $dto->quantity),
            'variation' => [
                'id' => $dto->variationId,
                'data' => $currentVariation instanceof ProductVariation
                    ? $currentVariation->productAttributes->map(fn($item)
                        => [
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

    private function prepareCartInfoForNonVariationProduct(ProductCartDto $dto): UserCartInfoDto
    {
        if (! $this->stockService->hasEnoughSuppliesForRequestedQuantity($dto->product, $dto->quantity)) {
            throw new ProductCannotBeAddedToCartException();
        }

        return UserCartInfoDto::fromArray([
            'product_id' => $dto->product->id,
            'product_image' => $dto->product->preview_image,
            'product_title' => $dto->product->title,
            'quantity' => $dto->quantity,
            'price_per_unit' => $dto->product->warehouse->default_price,
            'variation' => null,
            'final_price' => $this->countFinalPrice($dto->product->warehouse->default_price, $dto->quantity),
            'discount' => $this->loadDiscountIfExists($dto->product, $dto->quantity),
        ]);
    }

    private function prepareCartItemsBeforeUpdate(CartProductsQuantityDto $dto): BaseCollection
    {
        $cartItems = $dto->cartItems;

        $preparedProducts = $this->warehouseService->getWarehouseInfoAboutProducts(
            products: new Collection($cartItems->pluck('product')),
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'quantity'], []],
                combinedAttrCols: [['id', 'quantity'], []],
            ),
        )->keyBy('id');

        return $cartItems->map(fn($item)
            => [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'product' => $preparedProducts->get($item->product->id, $item->product),
            'variation' => $item->variation,
        ]);
    }
}