<?php

namespace Modules\Warehouse\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Warehouse\Models\ProductVariation;

class UniqueSkuInCombinationsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $skus = collect($value)->pluck('sku');

        if (ProductVariation::query()->whereIn('sku', $skus)->exists()) {
            $fail('Some of SKU is already exists.');
        }
    }
}
