<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Modules\Order\Cart\Models\Cart;
use Modules\User\Users\Models\User;

class CartExistsInDbRule implements ValidationRule
{
    public function __construct(
        private ?User $user,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cartItems = collect($value)->pluck('cart_id');

        foreach ($cartItems as $cartId) {
            if (! Str::isUuid($cartId)) {
                $fail('Invalid cart ID.');

                return;
            }
        }

        $cartsInDbCount = Cart::query()
            ->where('user_id', $this->user->id)
            ->whereIn('id', $cartItems->toArray())
            ->count();

        if ($cartsInDbCount !== $cartItems->count()) {
            $fail('Some of cart items are not exists.');
        }
    }
}
