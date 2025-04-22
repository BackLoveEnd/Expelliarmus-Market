<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\User\Contracts\UserInterface;
use Modules\User\Models\Guest;
use Modules\User\Models\User;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use RuntimeException;

class OrderService
{
    private UserInterface $user;

    public function __construct(
        private CartStorageService $storageService,
        private WarehouseStockService $stockService,
        private WarehouseProductInfoService $warehouseService,
        private DiscountedProductsService $discountService,
        private OrderPersistService $orderPersistService,
    ) {}

    public function for(UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function process(): string
    {
        if ($this->user instanceof User) {
            return $this->userOrderFactory($this->user);
        }

        if ($this->user instanceof Guest) {
            return $this->guestOrderFactory($this->user);
        }

        throw new RuntimeException('User not provided');
    }

    private function userOrderFactory(User $user): string
    {
        return (new OrderRegularUserCreateService(
            cartStorage: $this->storageService,
            prepareOrderService: new PrepareOrderService(
                cartStorage: $this->storageService,
                availabilityCheckerService: new ProductsAvailabilityCheckerService(
                    stockService: $this->stockService,
                    warehouseService: $this->warehouseService,
                ),
            ),
            orderPriceService: new OrderLineService(
                discountService: $this->discountService,
            ),
            orderPersistService: $this->orderPersistService,
        ))->create($user);
    }

    private function guestOrderFactory(Guest $guest): string
    {
        return (new OrderGuestCreateService(
            cartStorage: $this->storageService,
            prepareOrderService: new PrepareOrderService(
                cartStorage: $this->storageService,
                availabilityCheckerService: new ProductsAvailabilityCheckerService(
                    stockService: $this->stockService,
                    warehouseService: $this->warehouseService,
                ),
            ),
            orderPriceService: new OrderLineService(
                discountService: $this->discountService,
            ),
            orderPersistService: $this->orderPersistService,
        ))->create($guest);
    }
}