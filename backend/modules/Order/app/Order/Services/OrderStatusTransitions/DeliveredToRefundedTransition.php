<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\OrderStatusTransitions;

use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use RuntimeException;

class DeliveredToRefundedTransition implements OrderStatusTransitionInterface
{
    public function execute(Order $order): Order
    {
        if (! $order->status->is(OrderStatusEnum::DELIVERED)) {
            throw new RuntimeException('Status Transition not allowed');
        }

        $order->status = OrderStatusEnum::REFUNDED;

        $order->save();

        return $order;
    }
}
