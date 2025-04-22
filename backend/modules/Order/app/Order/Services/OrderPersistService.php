<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\User\Contracts\UserInterface;

class OrderPersistService
{
    public function saveCheckout(UserInterface $user, Collection $orderLines): void
    {
        DB::transaction(function () use ($user, $orderLines) {
            $order = $this->createOrder($user, $orderLines->sum('totalPrice'));

            $data = $orderLines->map(function (OrderLineDto $dto) use ($order) {
                return [
                    'product_id' => $dto->product->id,
                    'quantity' => $dto->quantity,
                    'total_price' => $dto->totalPrice,
                    'price_per_unit_at_order_time' => $dto->unitPriceAtOrderTime,
                    'order_id' => $order->id,
                    'variation' => $dto->variation ? json_encode($dto->variation) : null,
                ];
            });

            OrderLine::query()->insert($data->toArray());
        });
    }

    private function createOrder(UserInterface $user, float $totalPrice): Order
    {
        return $user->orders()->create([
            'total_price' => $totalPrice,
            'status' => OrderStatusEnum::PENDING,
            'created_at' => now(),
        ]);
    }
}