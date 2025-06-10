<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Spatie\QueryBuilder\Filters\Filter;

class DiscountStatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property)
    {
        return $query->where('status', DiscountStatusEnum::fromString($value));
    }
}
