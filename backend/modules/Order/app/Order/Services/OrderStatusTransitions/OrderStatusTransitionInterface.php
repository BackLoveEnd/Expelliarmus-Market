<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\OrderStatusTransitions;

use Modules\Order\Order\Models\Order;

interface OrderStatusTransitionInterface
{
    public function execute(Order $order): Order;
}
