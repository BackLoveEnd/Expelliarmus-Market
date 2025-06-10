<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Illuminate\Support\Facades\DB;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Dto\OrderLinesDto;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;

class OrderPersistService
{
    public function __construct(
        private WarehouseStockService $warehouseStockService,
    ) {}

    public function saveCheckout(UserInterface $user, OrderLinesDto $dto): int
    {
        return DB::transaction(function () use ($user, $dto) {
            $order = $this->createOrder($user, $dto->totalPrice);

            $data = $dto->orderLines->map(function (OrderLineDto $dto) use ($order) {
                return [
                    'product_id' => $dto->product->id,
                    'quantity' => $dto->quantity,
                    'total_price' => $dto->totalPrice,
                    'price_per_unit_at_order_time' => $dto->unitPriceAtOrderTime,
                    'order_id' => $order->id,
                    'variation' => $dto->variation ? json_encode($dto->variation) : null,
                ];
            });

            $this->warehouseStockService->decreaseProductsStock(
                productsWithQuantities: $dto->orderLines->select(['product', 'quantity']),
            );

            OrderLine::query()->insert($data->toArray());

            return $order->order_id;
        });
    }

    public function syncGuestOrdersWithRegularUser(Guest $guest, User $user): int
    {
        return Order::query()->client($guest)->update([
            'userable_type' => User::class,
            'userable_id' => $user->id,
        ]);
    }

    private function createOrder(UserInterface $user, float $totalPrice): Order
    {
        return $user->orders()->create([
            'total_price' => $totalPrice,
            'contact_email' => $user->email,
            'status' => OrderStatusEnum::PENDING,
            'created_at' => now(),
        ]);
    }
}
