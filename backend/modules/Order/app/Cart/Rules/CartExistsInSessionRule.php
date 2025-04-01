<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Rules;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Validation\ValidationRule;

class CartExistsInSessionRule implements ValidationRule
{
    public function __construct(
        private Session $session,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cartItems = collect($value)->mapWithKeys(fn($item) => [$item['cart_id'] => $item['product_id']]);

        $cartsInSession = collect($this->session->get('user.cart', []));

        $cartsInSessionData = $cartsInSession->mapWithKeys(fn($cart) => [$cart->id => $cart->product_id ?? null]);

        $missingIds = $cartItems->keys()->diff($cartsInSessionData->keys());

        if ($missingIds->isNotEmpty()) {
            $fail('Some of the carts do not exist in session.');
        }

        $invalidPairs = $cartItems->filter(fn($productId, $cartId)
            => ! isset($cartsInSessionData[$cartId]) || $cartsInSessionData[$cartId] !== $productId,
        );

        if ($invalidPairs->isNotEmpty()) {
            $fail('Some cart item and product pairs do not match in session.');
        }
    }
}