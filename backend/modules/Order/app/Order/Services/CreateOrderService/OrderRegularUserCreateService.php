<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Order\Order\Events\OrderCreated;
use Modules\Order\Order\Exceptions\CartMustNotBeEmptyBeforeOrderException;
use Modules\Order\Order\Exceptions\FailedToCreateOrderException;
use Modules\Order\Order\Exceptions\ProductCannotBeProcessedToCheckoutException;
use Modules\Order\Order\Exceptions\ProductHasNotEnoughSuppliesException;
use Modules\User\Users\Models\User;
use Throwable;

class OrderRegularUserCreateService
{
    public function __construct(
        private CartStorageService $cartStorage,
        private PrepareOrderService $prepareOrderService,
        private OrderLineService $orderPriceService,
        private OrderPersistService $orderPersistService,
    ) {}

    public function create(User $user, ?string $couponCode): int
    {
        try {
            $orderItemsPrepared = $this->prepareOrderService->prepare($user);

            $orderLines = $this->orderPriceService->prepareOrderLines(
                orderItems: $orderItemsPrepared,
                user: $user,
                couponCode: $couponCode,
            );

            $orderId = $this->orderPersistService->saveCheckout($user, $orderLines);

            event(new OrderCreated($user, $orderId, $orderLines));

            $this->cartStorage->clearCart($user);

            return $orderId;
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
