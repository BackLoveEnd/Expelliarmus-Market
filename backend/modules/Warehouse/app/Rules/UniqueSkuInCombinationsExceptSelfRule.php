<?php

declare(strict_types=1);

namespace Modules\Warehouse\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Warehouse\Models\ProductVariation;

class UniqueSkuInCombinationsExceptSelfRule implements ValidationRule
{
    public function __construct(
        private int $productId
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $skus = collect($value)->pluck('sku');

        $existsSku = ProductVariation::query()
            ->whereIn('sku', $skus)
            ->whereNot('product_id', $this->productId)
            ->exists();

        if ($existsSku) {
            $fail('Some of SKU is already exists.');
        }
    }
}
