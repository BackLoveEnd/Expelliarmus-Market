<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DiscountFinishedFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        if (! is_null($value)) {
            return $query->when(
                value: $value === true,
                callback: fn ($value) => $query->whereDate('end_date', '<', now()),
                default: fn ($value) => $query->whereDate('end_date', '>', now()),
            );
        }

        return $query;
    }
}
