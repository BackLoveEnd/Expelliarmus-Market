<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Spatie\QueryBuilder\Filters\Filter;

class WarehouseStatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (WarehouseProductStatusEnum::tryFrom((int) $value) !== null) {
            $query->where('warehouses.status', $value);
        }
    }
}
