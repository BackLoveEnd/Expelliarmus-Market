<?php

namespace Modules\Warehouse\Enums;

enum ProductStatusEnum: int
{
    case PUBLISHED = 0;

    case NOT_PUBLISHED = 1;

    case TRASHED = 2;

    public function toString(): string
    {
        return match ($this) {
            self::PUBLISHED => 'Published',
            self::NOT_PUBLISHED => 'Not Published',
            self::TRASHED => 'Trashed'
        };
    }

    public function toColorType(): string
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::NOT_PUBLISHED => 'warning',
            self::TRASHED => 'danger'
        };
    }
}
