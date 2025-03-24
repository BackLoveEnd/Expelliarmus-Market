<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Modules\Product\Models\Product;
use Spatie\QueryBuilder\QueryBuilder;

class ProductsRetriever
{
    public function __construct(
        public readonly FiltersConnector $filtersConnector,
        public readonly SortsConnector $sortsConnector,
    ) {}

    public function retrieve(array $columns = ['*'])
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters($this->filtersConnector->filters())
            ->allowedSorts($this->sortsConnector->sorts())
            ->paginate(config('product.retrieve.shop.default'), [
                ...$columns,
                'category_id',
                'with_attribute_combinations',
            ]);
    }
}