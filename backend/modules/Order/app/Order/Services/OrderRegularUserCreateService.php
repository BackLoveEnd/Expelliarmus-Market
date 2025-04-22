<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Events\OrderCreated;
use Modules\User\Models\User;
use Throwable;

class OrderRegularUserCreateService
{
    public function __construct(
        private PrepareOrderService $prepareOrderService,
        private OrderLineService $orderPriceService,
        private OrderPersistService $orderPersistService,
    ) {}

    public function create(User $user): string
    {
        try {
            $orderItemsPrepared = $this->prepareOrderService->prepare($user);

            /**@var Collection<int, OrderLineDto> $orderLines */
            $orderLines = $this->orderPriceService->prepareOrderLines($orderItemsPrepared);

            $orderId = $this->orderPersistService->saveCheckout($user, $orderLines);

            event(new OrderCreated($user, $orderLines));
            // clear cart

            return $orderId;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}