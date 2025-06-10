<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AttributeInCombinationUniqueRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $collection = collect($value);

        $withoutIdAttr = $collection->filter(fn ($item) => $item['id'] !== null);

        if ($withoutIdAttr->uniqueStrict('id')->count() !== $withoutIdAttr->count()) {
            $fail('Attributes in each combination must be unique.');

            return;
        }

        $attributes = $collection->map(fn ($item) => mb_strtolower($item['name']));

        if ($attributes->duplicates()->isNotEmpty()) {
            $fail('Attributes in each combination must be unique.');
        }
    }
}
