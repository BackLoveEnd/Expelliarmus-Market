<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\TestCase;
use Modules\Product\Rules\ValueHasCorrectDataTypeRule;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class AttributeValueValidationTest extends TestCase
{
    public function test_validate_hex(): void
    {
        $type = ProductAttributeTypeEnum::COLOR;

        $rule = new ValueHasCorrectDataTypeRule($type);

        // Imitate behaviour when laravel throws exception using $fail closure
        $fail = fn () => throw new RuntimeException('Test exception');

        $this->assertThrows(
            test: fn () => $rule->validate('', '#FFFFZ', $fail),
            expectedClass: RuntimeException::class
        );
    }

    public function test_validate_float(): void
    {
        $type = ProductAttributeTypeEnum::DECIMAL;

        $rule = new ValueHasCorrectDataTypeRule($type);

        // Imitate behaviour when laravel throws exception using $fail closure
        $fail = fn () => throw new RuntimeException('Test exception');

        $this->assertThrows(
            test: fn () => $rule->validate('', 'a', $fail),
            expectedClass: RuntimeException::class
        );
    }

    public function test_validate_int(): void
    {
        $type = ProductAttributeTypeEnum::NUMBER;

        $rule = new ValueHasCorrectDataTypeRule($type);

        // Imitate behaviour when laravel throws exception using $fail closure
        $fail = fn () => throw new RuntimeException('Test exception');

        $this->assertThrows(
            test: fn () => $rule->validate('', 1.5, $fail),
            expectedClass: RuntimeException::class
        );
    }

    public function test_validate_string(): void
    {
        $type = ProductAttributeTypeEnum::STRING;

        $rule = new ValueHasCorrectDataTypeRule($type);

        // Imitate behaviour when laravel throws exception using $fail closure
        $fail = fn () => throw new RuntimeException('Test exception');

        $this->assertThrows(
            test: fn () => $rule->validate('', null, $fail),
            expectedClass: RuntimeException::class
        );
    }
}
