<?php

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum as Enum;

class ValueHasCorrectDataTypeRule implements ValidationRule
{
    public function __construct(
        private Enum $type
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $method = match ($this->type) {
            Enum::STRING => 'stringValidate',
            Enum::NUMBER => 'intValidate',
            Enum::COLOR => 'hexValidate',
            Enum::DECIMAL => 'floatValidate',
        };

        if (! method_exists($this, $method)) {
            $fail('Unknown attribute value type.');
        }

        $this->$method($value, $fail);
    }

    private function hexValidate(mixed $value, Closure $fail): void
    {
        if (! preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $value)) {
            $fail('Attribute value must be valid color.');
        }
    }

    private function floatValidate(mixed $value, Closure $fail): void
    {
        if (is_int($value)) {
            $value = (float) $value;
        }

        if (! is_float($value)) {
            $fail('Attribute value must be valid decimal number.');
        }
    }

    private function intValidate(mixed $value, Closure $fail): void
    {
        if (! is_int($value)) {
            $fail('Attribute value must be valid integer number.');
        }
    }

    private function stringValidate(mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Attribute value must be string.');
        }
    }
}
