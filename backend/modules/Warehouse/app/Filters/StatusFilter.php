<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class StatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        return $query->where('products.status', $value);
    }
}