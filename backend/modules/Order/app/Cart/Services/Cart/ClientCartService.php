<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services\Cart;

use Modules\Order\Cart\Dto\CartProductsQuantityDto;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\User\Users\Models\User;

class ClientCartService
{
    public function __construct(
        protected CartStorageService $storage,
        protected AddingPossibilityProductToCartCheckerService $checker,
        protected DiscountCartService $discountCalculator,
        protected CartDataPrepareService $prepareService,
    ) {}

    public function getCart(?User $user): array
    {
        return $this->storage->getCart($user);
    }

    public function addToCart(?User $user, ProductCartDto $dto): void
    {
        $this->checker->ensureProductCanBeAddedToCart($dto->product);

        if ($this->storage->productExistsInCart($user, $dto->product, $dto->variationId)) {
            $this->storage->addQuantityToExistProduct($user, $dto);

            return;
        }

        $cartInfo = is_null($dto->product->hasCombinedAttributes())
            ? $this->prepareService->prepareCartInfoForNonVariationProduct($dto)
            : $this->prepareService->prepareCartInfoForProductWithVariations($dto);

        $this->storage->saveCart($user, $cartInfo);
    }

    public function updateProductsQuantities(?User $user, CartProductsQuantityDto $dto): void
    {
        $cartsInSession = $this->storage->getCart($user);
        if (! $cartsInSession) {
            return;
        }

        $updatedCartItems = $this->prepareService->prepareCartItemsBeforeUpdate($dto);

        $this->checker->ensureCanUpdateProductsQuantity($updatedCartItems);

        $updatedCart = $this->discountCalculator->updateQuantitiesAndPrices($dto, collect($cartsInSession));

        $this->storage->updateCart($user, $updatedCart);
    }

    public function removeFromCart(?User $user, string $id): void
    {
        $this->storage->removeFromCart($user, $id);
    }

    public function clearCart(?User $user): void
    {
        $this->storage->clearCart($user);
    }

    public function isCartEmpty(?User $user): bool
    {
        return $this->storage->isCartEmpty($user);
    }
}
