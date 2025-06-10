<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Dto;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Http\Requests\UpdateProductsQuantityRequest;
use Modules\Order\Cart\Models\Cart;
use Modules\Product\Models\Product;

final readonly class CartProductsQuantityDto
{
    public function __construct(public Collection $cartItems) {}

    public static function fromRequest(UpdateProductsQuantityRequest $request): CartProductsQuantityDto
    {
        $cartItems = collect($request->items);

        if ($request->user()) {
            $products = Cart::query()
                ->with('product:id,with_attribute_combinations,product_article,slug')
                ->whereIn('id', $cartItems->pluck('cart_id')->toArray())
                ->get(['id', 'product_id', 'variation']);
        } else {
            $products = collect($request->session()->get('user.cart', []))
                ->whereIn('id', $cartItems->pluck('cart_id')->toArray());

            $productModels = Product::query()->whereIn('id', $products->pluck('product_id'))
                ->get(['id', 'with_attribute_combinations', 'product_article', 'slug']);

            $products = $products->map(function (object $productCart) use ($productModels) {
                return (object) [
                    'id' => $productCart->id,
                    'product' => $productModels->firstWhere('id', $productCart->product_id),
                    'variation' => $productCart->variation,
                ];
            });
        }

        $cartItemsWithProducts = $cartItems->map(function ($item) use ($products) {
            $productCartItem = $products->firstWhere('id', $item['cart_id']);

            return (object) [
                'id' => $item['cart_id'],
                'quantity' => $item['quantity'],
                'product' => $productCartItem->product,
                'variation' => $productCartItem->variation['id'] ?? null,
            ];
        });

        return new self(
            cartItems: $cartItemsWithProducts,
        );
    }
}
