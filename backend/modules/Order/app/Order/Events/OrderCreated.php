<?php

namespace Modules\Order\Order\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Order\Dto\OrderLinesDto;
use Modules\User\Users\Contracts\UserInterface;

class OrderCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly UserInterface $user,
        public readonly int $orderId,
        public readonly OrderLinesDto $orderLines,
    ) {}
}
