<?php

namespace Modules\ContentManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class OnlySpecificStorageUrlRule implements ValidationRule
{
    public function __construct(
        private string $storageUrl,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Str::startsWith($value, $this->storageUrl)) {
            $fail("Given image url is not related to $this->storageUrl path.");
        }
    }
}
