<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Modules\Order\Order\Services\CreateOrderService\OrderPersistService;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;

class GuestMigrateOrdersService
{
    public function __construct(
        private OrderPersistService $orderPersistService,
    ) {}

    public function process(Guest $guest, User $user): void
    {
        $this->orderPersistService->syncGuestOrdersWithRegularUser($guest, $user);
    }
}
