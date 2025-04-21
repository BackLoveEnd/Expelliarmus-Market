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

    public function process(): void
    {
        if ($this->user instanceof User) {
            $this->userOrderFactory($this->user);

            return;
        }

        if ($this->user instanceof Guest) {
            $this->guestOrderFactory($this->user);

            return;
        }

        throw new RuntimeException('User not provided');
    }

    private function userOrderFactory(User $user): void
    {
        (new OrderRegularUserCreateService(
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

    private function guestOrderFactory(Guest $guest): void
    {
        (new OrderGuestCreateService(
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