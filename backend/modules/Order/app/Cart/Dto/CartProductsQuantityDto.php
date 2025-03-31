<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Dto;

use Illuminate\Support\Collection;
use Modules\Order\Cart\Http\Requests\UpdateProductsQuantityRequest;

final readonly class CartProductsQuantityDto
{
    public function __construct(public Collection $cartItems) {}

    public static function fromRequest(UpdateProductsQuantityRequest $request): CartProductsQuantityDto
    {
        $cartItems = collect($request->items);

        $productIds = $cartItems->pluck('product_id')->unique();

        /*$products = Product::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'with_attribute_combinations'])
            ->keyBy('id');

        $cartItems = Cart::query()
            ->with('product:id,with_attribute_combination')
            ->whereIn('id', $cartItems->pluck('cart_id'))
            ->get();*/

        $cartItemsWithProducts = $cartItems->map(function ($item) use ($products) {
            return (object)[
                'cart_id' => $item['cart_id'],
                'quantity' => $item['quantity'],
                'product' => $products->get($item['product_id']),
            ];
        });

        return new self(
            cartItems: $cartItemsWithProducts,
        );
    }
}