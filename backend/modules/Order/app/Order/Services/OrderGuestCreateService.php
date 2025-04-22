<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Events\OrderCreated;
use Modules\Order\Order\Exceptions\FailedToCreateOrderException;
use Modules\User\Models\Guest;
use Throwable;

class OrderGuestCreateService
{
    public function __construct(
        private PrepareOrderService $prepareOrderService,
        private OrderLineService $orderPriceService,
        private OrderPersistService $orderPersistService,
    ) {}

    public function create(Guest $user): string
    {
        try {
            $orderItemsPrepared = $this->prepareOrderService->prepare(null);

            /**@var Collection<int, OrderLineDto> $orderLines */
            $orderLines = $this->orderPriceService->prepareOrderLines($orderItemsPrepared);

            $orderId = $this->orderPersistService->saveCheckout($user, $orderLines);

            event(new OrderCreated($user, $orderId, $orderLines));

            return $orderId;
            // clear cart
        } catch (Throwable $e) {
            throw new FailedToCreateOrderException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
