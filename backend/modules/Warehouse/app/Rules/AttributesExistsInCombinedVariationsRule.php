<?php

namespace Modules\Warehouse\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Warehouse\Models\ProductAttribute;

class AttributesExistsInCombinedVariationsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attributes = collect($value)->pluck('attributes')->collapse();

        $uniqueAttributesId = $attributes->whereNotNull('id')->unique('id')->pluck('id');

        if ($uniqueAttributesId->isEmpty()) {
            return;
        }

        if (ProductAttribute::query()->whereIn('id', $uniqueAttributesId)->count() !== $uniqueAttributesId->count()) {
            $fail('Some of product attributes in your variations does not exists.');
        }
    }
}
