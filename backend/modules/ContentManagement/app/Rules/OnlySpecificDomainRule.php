<?php

namespace Modules\ContentManagement\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnlySpecificDomainRule implements ValidationRule
{
    public function __construct(
        private string $allowedDomain
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allow = parse_url($this->allowedDomain, PHP_URL_HOST);

        $host = parse_url($value, PHP_URL_HOST);

        if ($host !== $allow) {
            $fail("Url allowed only from $allow.");
        }
    }
}
