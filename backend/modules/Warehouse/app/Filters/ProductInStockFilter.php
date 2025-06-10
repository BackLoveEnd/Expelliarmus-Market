<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ProductInStockFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        if (! is_null($value)) {
            return $query->when(
                value: $value === true,
                callback: fn ($query) => $query->where('warehouses.total_quantity', '>', 0),
                default: fn ($query) => $query->where('warehouses.total_quantity', 0),
            );
        }

        return $query;
    }
}
