<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Modules\Order\Models\Cart;

class CartExistsInDbRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cartItems = collect($value)->mapWithKeys(fn($item) => [$item['cart_id'] => $item['product_id']]);

        foreach ($cartItems as $cartId => $productId) {
            if (! Str::isUuid($cartId)) {
                $fail('Invalid cart ID.');
                return;
            }

            if (! is_int($productId)) {
                $fail('Invalid product ID.');
            }
        }

        $cartsInDb = Cart::query()
            ->whereIn('cart_id', $cartItems->keys()->toArray())
            ->get(['cart_id', 'product_id'])
            ->mapWithKeys(fn($cart) => [$cart->cart_id => $cart->product_id]);

        if ($cartsInDb->count() !== $cartItems->count()) {
            $fail('Some carts do not exist.');
        }

        $invalidPairs = $cartItems->filter(fn($productId, $cartId)
            => ! isset($cartsInDb[$cartId]) || $cartsInDb[$cartId] !== $productId,
        );

        if ($invalidPairs->isNotEmpty()) {
            $fail('Some cart item and product pairs do not match.');
        }
    }
}