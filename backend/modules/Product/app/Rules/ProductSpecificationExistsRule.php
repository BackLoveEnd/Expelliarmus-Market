<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Product\Models\ProductSpecAttributes;

class ProductSpecificationExistsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $specifications = collect($value)->pluck('specifications')->collapse();

        $idOfExistsSpecifications = $specifications->whereNotNull('id')
            ->pluck('id')
            ->unique();

        $specsFromDb = ProductSpecAttributes::query()->whereIn('id', $idOfExistsSpecifications)
            ->get(['id']);

        if ($specsFromDb->count() !== $idOfExistsSpecifications->count()) {
            $fail('Some of specifications that you choose does not exists.');
        }
    }
}
