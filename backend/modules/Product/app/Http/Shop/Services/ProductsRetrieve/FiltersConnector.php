<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Spatie\QueryBuilder\AllowedFilter;

readonly class FiltersConnector
{
    /**@var array<int, AllowedFilter> $filters */
    private array $filters;

    public function defineFilters(array $filters = []): void
    {
        $this->filters = $filters;
    }

    public function filters(): array
    {
        return $this->filters;
    }
}