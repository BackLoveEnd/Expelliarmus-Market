<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllSpecificationsInGroupAreUniqueRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $collection = collect($value);

        if ($collection->whereNull('id')->isEmpty()) {
            if ($collection->duplicates('id')->isNotEmpty()) {
                $fail('All specifications inside group must be unique.');
            }

            return;
        }

        $notUniqueSpecs = $collection->map(fn ($spec) => mb_strtolower($spec['spec_name']))
            ->duplicates();

        if ($notUniqueSpecs->isNotEmpty()) {
            $fail('All specifications inside group must be unique.');
        }
    }
}
