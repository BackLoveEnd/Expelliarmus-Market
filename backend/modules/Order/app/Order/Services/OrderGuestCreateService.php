<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Events\OrderCreated;
use Modules\Order\Order\Exceptions\CartMustNotBeEmptyBeforeOrderException;
use Modules\Order\Order\Exceptions\FailedToCreateOrderException;
use Modules\Order\Order\Exceptions\ProductCannotBeProcessedToCheckoutException;
use Modules\Order\Order\Exceptions\ProductHasNotEnoughSuppliesException;
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
            if ($e instanceof CartMustNotBeEmptyBeforeOrderException
                || $e instanceof ProductCannotBeProcessedToCheckoutException
                || $e instanceof ProductHasNotEnoughSuppliesException) {
                throw $e;
            }

            throw new FailedToCreateOrderException(message: $e->getMessage(), previous: $e);
        }
    }
}
