<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MaxPriceViewFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (is_numeric($value)) {
            $float = (float)$value;

            if ($float !== 0.0) {
                $query->where('product_min_prices.min_price', '<=', $float);
            }
        }
    }
}