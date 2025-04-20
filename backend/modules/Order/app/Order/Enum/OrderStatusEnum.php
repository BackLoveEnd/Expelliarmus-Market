<?php

namespace Modules\Order\Order\Enum;

enum OrderStatusEnum: int
{
    case PENDING = 0;

    case PAID = 1;

    case DELIVERED = 2;

    case CANCELED = 3;

    case REFUNDED = 4;

    case IN_PROGRESS = 5;

    public function toString(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::PAID => 'paid',
            self::DELIVERED => 'delivered',
            self::CANCELED => 'canceled',
            self::REFUNDED => 'refunded',
            self::IN_PROGRESS => 'in progress',
        };
    }
}
