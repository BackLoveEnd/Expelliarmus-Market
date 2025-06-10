<?php

declare(strict_types=1);

namespace Modules\Warehouse\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class TotalQuantitySort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->orderBy('warehouses.total_quantity', $descending ? 'desc' : 'asc');
    }
}
