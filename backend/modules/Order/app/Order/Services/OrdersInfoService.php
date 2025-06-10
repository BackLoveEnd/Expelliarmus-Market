<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\Order\Order\Filters\SearchFilter;
use Modules\Order\Order\Models\Order;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersInfoService
{
    public function pendings(int $limit, int $offset): LimitOffsetDto
    {
        return $this->getOrdersByStatus([OrderStatusEnum::PENDING, OrderStatusEnum::IN_PROGRESS], $limit, $offset);
    }

    public function cancelled(int $limit, int $offset): LimitOffsetDto
    {
        return $this->getOrdersByStatus(OrderStatusEnum::CANCELED, $limit, $offset);
    }

    public function delivered(int $limit, int $offset): LimitOffsetDto
    {
        return $this->getOrdersByStatus(OrderStatusEnum::DELIVERED, $limit, $offset);
    }

    public function refunded(int $limit, int $offset): LimitOffsetDto
    {
        return $this->getOrdersByStatus(OrderStatusEnum::REFUNDED, $limit, $offset);
    }

    public function getOrderLinesByOrder(Order $order): Collection
    {
        return $order
            ->orderLines()
            ->with(['product' => fn ($query) => $query->select('id', 'title', 'preview_image', 'slug')])
            ->get();
    }

    /**
     * @param  array<int, OrderStatusEnum>|OrderStatusEnum  $status
     */
    protected function getOrdersByStatus(array|OrderStatusEnum $status, int $limit, int $offset): LimitOffsetDto
    {
        if ($status instanceof OrderStatusEnum) {
            $status = Arr::wrap($status);
        }

        $orders = QueryBuilder::for(Order::class)
            ->with(
                [
                    'userable' => fn (MorphTo $morphTo) => $morphTo->select(
                        ['id', 'email', 'phone_number', 'first_name', 'last_name'],
                    ),
                ],
            )
            ->allowedSorts(['total_price', 'created_at'])
            ->allowedFilters([AllowedFilter::custom('search', new SearchFilter)])
            ->where(function (Builder $builder) use ($status) {
                foreach ($status as $index => $item) {
                    if ($index === 0) {
                        $builder->where('status', $item);
                    } else {
                        $builder->orWhere('status', $item);
                    }
                }
            })
            ->limit($limit)
            ->offset($offset)
            ->get();

        $orders = $orders->groupBy(fn (Order $order) => $order->userable_type.':'.$order->userable_id);

        return new LimitOffsetDto(
            items: $orders,
            total: Order::query()->where('status', $status)->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}
