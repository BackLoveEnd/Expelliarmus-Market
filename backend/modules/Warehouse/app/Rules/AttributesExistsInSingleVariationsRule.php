<?php

namespace Modules\Warehouse\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AttributesExistsInSingleVariationsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        dd($value);
    }
}
