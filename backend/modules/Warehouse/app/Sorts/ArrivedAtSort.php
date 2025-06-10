<?php

declare(strict_types=1);

namespace Modules\Warehouse\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ArrivedAtSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->orderBy('warehouses.arrived_at', $descending ? 'desc' : 'asc');
    }
}
