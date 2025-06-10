<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Spatie\QueryBuilder\QueryBuilder;

readonly class ProductsRetriever
{
    public function __construct(
        public FiltersConnector $filtersConnector,
        public SortsConnector $sortsConnector,
    ) {}

    public function retrieve(int $retrieveNum, array $columns = ['*']): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->join('product_min_prices', 'products.id', '=', 'product_min_prices.product_id')
            ->allowedFilters($this->filtersConnector->filters())
            ->allowedSorts($this->sortsConnector->sorts())
            ->whereStatus(ProductStatusEnum::PUBLISHED)
            ->paginate($retrieveNum, [
                ...$columns,
                'products.category_id',
                'products.with_attribute_combinations',
                'product_min_prices.min_price',
            ]);
    }
}
