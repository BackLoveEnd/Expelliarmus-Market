<?php

namespace Modules\Warehouse\Enums;

enum WarehouseProductStatusEnum: int
{
    case IN_STOCK = 0;

    case PENDING = 1;

    case NOT_AVAILABLE = 2;

    case PARTIALLY = 3;

    public function is(self $status): bool
    {
        return $this === $status;
    }

    public function isIn(array $statuses): bool
    {
        if (in_array($this, $statuses, true)) {
            return true;
        }

        return false;
    }

    public function toString(): string
    {
        return match ($this) {
            self::IN_STOCK => 'In Stock',
            self::PENDING => 'Pending',
            self::NOT_AVAILABLE => 'Not Available',
            self::PARTIALLY => 'Partially',
        };
    }

    public function toColorType(): string
    {
        return match ($this) {
            self::IN_STOCK => 'success',
            self::PENDING => 'warning',
            self::NOT_AVAILABLE => 'danger',
            self::PARTIALLY => 'info',
        };
    }
}
