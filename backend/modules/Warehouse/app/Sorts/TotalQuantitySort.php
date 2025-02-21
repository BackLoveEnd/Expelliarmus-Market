<?php

declare(strict_types=1);

namespace Modules\Warehouse\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class TotalQuantitySort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->join('warehouses', 'warehouses.product_id', '=', 'products.id')
            ->orderBy('warehouses.total_quantity', $descending ? 'desc' : 'asc');
    }
}