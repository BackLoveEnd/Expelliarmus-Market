<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\OrderStatusTransitions;

use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use RuntimeException;

class PendingToInProgressTransition implements OrderStatusTransitionInterface
{
    public function execute(Order $order): Order
    {
        if (! $order->status->is(OrderStatusEnum::PENDING)) {
            throw new RuntimeException('Status Transition not allowed');
        }

        $order->status = OrderStatusEnum::IN_PROGRESS;

        $order->save();

        return $order;
    }
}
