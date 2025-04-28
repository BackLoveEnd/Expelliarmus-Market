<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\User\Models\User;

class UserOrdersService
{
    public function getOrders(User $user, int $limit = 5): LengthAwarePaginator
    {
        $orders = $user
            ->orders()
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        $orderLines = OrderLine::query()
            ->with(['product' => fn($query) => $query->select('id', 'title', 'preview_image')])
            ->whereIn('order_id', $orders->pluck('id'))
            ->get();

        $orders->getCollection()->transform(function (Order $order) use ($orderLines) {
            $order->positions = $orderLines->where('order_id', $order->id)->values();

            return $order;
        });

        return $orders;
    }
}