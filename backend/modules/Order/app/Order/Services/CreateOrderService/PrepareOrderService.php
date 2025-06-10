<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Order\Order\Exceptions\CartMustNotBeEmptyBeforeOrderException;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;
use stdClass;

class PrepareOrderService
{
    public function __construct(
        private CartStorageService $cartStorage,
        private ProductsAvailabilityCheckerService $availabilityCheckerService,
    ) {}

    public function prepare(?User $user): Collection
    {
        if ($this->cartStorage->isCartEmpty($user)) {
            throw new CartMustNotBeEmptyBeforeOrderException;
        }

        $preparedFromCart = $this->prepareFromCart($user);

        $this->availabilityCheckerService
            ->ensureProductsCanBeProcessedToCheckout($preparedFromCart->pluck('product'));

        return $this->availabilityCheckerService->ensureHasEnoughSupplies($preparedFromCart);
    }

    private function prepareFromCart(?User $user): Collection
    {
        $cartItems = collect($this->cartStorage->getCart($user));

        $products = $cartItems->pluck('product_id');

        $products = Product::query()
            ->whereIn('id', $products)
            ->get([
                'id',
                'status',
                'title',
                'with_attribute_combinations',
                'product_article',
                'preview_image',
            ]);

        return $cartItems->map(function (stdClass $item) use ($products) {
            $product = clone $products->firstWhere('id', $item->product_id);

            return [
                'product' => $product,
                'quantity' => $item->quantity,
                'variation_id' => $item->variation['id'] ?? null,
            ];
        });
    }
}
