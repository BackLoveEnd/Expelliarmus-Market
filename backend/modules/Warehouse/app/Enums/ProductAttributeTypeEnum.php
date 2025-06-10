<?php

namespace Modules\Warehouse\Enums;

enum ProductAttributeTypeEnum: int
{
    case STRING = 0;

    case NUMBER = 1;

    case DECIMAL = 2;

    case COLOR = 3;

    public function toTypes(): string
    {
        return match ($this) {
            self::STRING => 'text',
            self::NUMBER => 'number',
            self::DECIMAL => 'float',
            self::COLOR => 'color'
        };
    }

    public function castToType(mixed $value): string|int|float
    {
        return match ($this) {
            self::NUMBER => (int) $value,
            self::COLOR, self::STRING => (string) $value,
            self::DECIMAL => round($value, 2)
        };
    }
}
