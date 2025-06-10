<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Services\Cart;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Dto\CartProductsQuantityDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;

class DiscountCartService
{
    public function __construct(protected DiscountedProductsService $discountService) {}

    public function loadDiscountIfExists(Product $product, int $quantity): ?array
    {
        $product = $this->discountService->loadLastActiveDiscountForProduct($product);

        if ($this->discountService->productHasActiveDiscount($product)) {
            $currentRelation = $product->getCurrentVariationRelation();

            if (! $currentRelation) {
                $lastDiscount = $product->lastActiveDiscount->first();
            } else {
                $lastDiscount = $currentRelation->lastActiveDiscount->first();
            }

            return [
                'id' => $lastDiscount->id,
                'percentage' => $lastDiscount->percentage,
                'new_price' => $lastDiscount->discount_price,
                'final_price' => $this->countFinalPrice($lastDiscount->discount_price, $quantity),
                'end_date' => $lastDiscount->end_date,
            ];
        }

        return null;
    }

    public function countFinalPrice(float $price, int $quantity): float
    {
        return round($price * $quantity, 2);
    }

    public function updateQuantitiesAndPrices(CartProductsQuantityDto $dto, Collection $cartItems): Collection
    {
        return $cartItems->map(function (object $cartItem) use ($dto) {
            $matchingItem = $dto->cartItems->firstWhere('id', $cartItem->id);

            if ($matchingItem) {
                $cartItem->quantity = $matchingItem->quantity;
                $cartItem->final_price = $this->countFinalPrice($cartItem->price_per_unit, $matchingItem->quantity);
            }

            return $cartItem;
        });
    }
}
