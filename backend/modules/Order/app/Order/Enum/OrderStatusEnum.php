<?php

namespace Modules\Order\Order\Enum;

enum OrderStatusEnum: int
{
    case PENDING = 0;

    case DELIVERED = 2;

    case CANCELED = 3;

    case REFUNDED = 4;

    case IN_PROGRESS = 5;

    public function is(OrderStatusEnum $status): bool
    {
        return $this === $status;
    }

    public function canBeChanged(): bool
    {
        return match ($this) {
            self::PENDING, self::IN_PROGRESS, self::DELIVERED => true,
            self::CANCELED, self::REFUNDED => false,
        };
    }

    public function toString(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::DELIVERED => 'delivered',
            self::CANCELED => 'canceled',
            self::REFUNDED => 'refunded',
            self::IN_PROGRESS => 'in progress',
        };
    }
}
