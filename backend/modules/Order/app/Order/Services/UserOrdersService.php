<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\User\Users\Models\User;

class UserOrdersService
{
    public function getOrders(User $user, int $limit = 5): LengthAwarePaginator
    {
        $orders = $user
            ->orders()
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return $this->linkOrderLinesToOrders(
            orders: $orders,
            orderLines: $this->loadOrderLinesForOrders($orders->getCollection()),
        );
    }

    public function getCancelledOrders(User $user, int $limit = 5): LengthAwarePaginator
    {
        $orders = $user
            ->orders()
            ->where('status', OrderStatusEnum::CANCELED)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return $this->linkOrderLinesToOrders(
            orders: $orders,
            orderLines: $this->loadOrderLinesForOrders($orders->getCollection()),
        );
    }

    protected function loadOrderLinesForOrders(Collection $orders, array $cols = ['*']): EloquentCollection
    {
        return OrderLine::query()
            ->with(['product' => fn ($query) => $query->select('id', 'title', 'preview_image')])
            ->whereIn('order_id', $orders->pluck('id'))
            ->get($cols);
    }

    protected function linkOrderLinesToOrders(
        LengthAwarePaginator $orders,
        EloquentCollection $orderLines,
    ): LengthAwarePaginator {
        $orders->getCollection()->transform(function (Order $order) use ($orderLines) {
            $order->positions = $orderLines->where('order_id', $order->id)->values();

            return $order;
        });

        return $orders;
    }
}
