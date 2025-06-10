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
        $cartItemsId = collect($value)->pluck('cart_id');

        $cartsInSession = $this->session->get('user.cart', []);

        $cartsInSessionIds = collect($cartsInSession)->pluck('id');

        $missingIds = $cartItemsId->diff($cartsInSessionIds);

        if ($missingIds->isNotEmpty()) {
            $fail('Some of cart items are not exists.');
        }
    }
}
