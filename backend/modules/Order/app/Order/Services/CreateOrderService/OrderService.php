<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\User\Coupons\Services\CouponManageService;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use RuntimeException;

class OrderService
{
    private UserInterface $user;

    private ?string $couponCode = null;

    public function __construct(
        private CartStorageService $storageService,
        private WarehouseStockService $stockService,
        private WarehouseProductInfoService $warehouseService,
        private DiscountedProductsService $discountService,
    ) {}

    public function for(UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function usingCoupon(?string $couponCode): static
    {
        $this->couponCode = $couponCode;

        return $this;
    }

    public function process(): int
    {
        if ($this->user instanceof User) {
            return $this->userOrderFactory($this->user);
        }

        if ($this->user instanceof Guest) {
            return $this->guestOrderFactory($this->user);
        }

        throw new RuntimeException('User not provided');
    }

    private function userOrderFactory(User $user): int
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
                couponService: new CouponManageService,
            ),
            orderPersistService: new OrderPersistService(
                warehouseStockService: $this->stockService,
            ),
        ))->create($user, $this->couponCode);
    }

    private function guestOrderFactory(Guest $guest): int
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
                couponService: new CouponManageService,
            ),
            orderPersistService: new OrderPersistService(
                warehouseStockService: $this->stockService,
            ),
        ))->create($guest, $this->couponCode);
    }
}
