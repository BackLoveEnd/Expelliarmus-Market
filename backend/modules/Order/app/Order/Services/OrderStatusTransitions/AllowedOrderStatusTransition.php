<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\OrderStatusTransitions;

use InvalidArgumentException;
use Modules\Order\Order\Enum\OrderStatusEnum as Status;

abstract class AllowedOrderStatusTransition
{
    private const array ALL = [
        [
            'current' => Status::IN_PROGRESS,
            'next' => Status::CANCELED,
            'transition' => InProgressToCanceledTransition::class,
        ],
        [
            'current' => Status::IN_PROGRESS,
            'next' => Status::DELIVERED,
            'transition' => InProgressToDeliveredTransition::class,
        ],
        [
            'current' => Status::PENDING,
            'next' => Status::CANCELED,
            'transition' => PendingToCancelledTransition::class,
        ],
        [
            'current' => Status::PENDING,
            'next' => Status::IN_PROGRESS,
            'transition' => PendingToInProgressTransition::class,
        ],
        [
            'current' => Status::DELIVERED,
            'next' => Status::REFUNDED,
            'transition' => DeliveredToRefundedTransition::class,
        ],
    ];

    public static function transition(Status $currentStatus, Status $nextStatus): OrderStatusTransitionInterface
    {
        $transition = collect(self::ALL)
            ->first(function (array $transition) use ($currentStatus, $nextStatus) {
                return $transition['current'] === $currentStatus && $transition['next'] === $nextStatus;
            });

        if (! $transition) {
            throw new InvalidArgumentException(
                "No order status transition for ".$currentStatus->toString()." - ".$nextStatus->toString(),
            );
        }

        return new $transition['transition']();
    }
}