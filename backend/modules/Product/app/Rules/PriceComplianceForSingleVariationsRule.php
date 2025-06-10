<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PriceComplianceForSingleVariationsRule implements ValidationRule
{
    public function __construct(
        private mixed $defaultPrice
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $prices = collect($value)->pluck('price');

        $filledPrices = $prices->filter(fn (?int $price) => $price !== null);

        if ($filledPrices->isEmpty()) {
            return;
        }

        if ($this->defaultPrice !== null && $prices->count() !== $filledPrices->count()) {
            $fail(
                'In some of your variations, the price is not specified, but a default price is indicated. Please define the price.'
            );
        }
    }
}
