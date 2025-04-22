<?php

namespace Modules\Order\Order\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\User\Contracts\UserInterface;

class OrderCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly UserInterface $user,
        public readonly string $orderId,
        public readonly Collection $orderLines,
    ) {}
}
