<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Spatie\QueryBuilder\Filters\Filter;

class StatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (ProductStatusEnum::tryFrom((int) $value) !== null) {
            $query->where('products.status', $value);
        }
    }
}
