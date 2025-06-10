<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Services\OrderStatusTransitions\AllowedOrderStatusTransition;

class ChangeOrderStatusAction
{
    public function handle(Order $order, OrderStatusEnum $nextStatus): Order
    {
        return DB::transaction(static function () use ($order, $nextStatus) {
            $transition = AllowedOrderStatusTransition::transition(
                currentStatus: $order->status,
                nextStatus: $nextStatus,
            );

            return $transition->execute($order);
        });
    }
}
