<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class PriceViewFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (is_array($value) && is_numeric($value[0]) && is_numeric($value[1])) {
            $minPrice = (float) $value[0];

            $maxPrice = (float) $value[1];

            $query->whereBetween('product_min_prices.min_price', [$minPrice, $maxPrice]);
        }
    }
}
