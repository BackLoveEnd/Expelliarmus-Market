<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Product;

use Illuminate\Config\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;
use Modules\Warehouse\Filters\ProductInStockFilter;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use Modules\Warehouse\Sorts\DeletedAtSort;
use Modules\Warehouse\Sorts\TotalQuantitySort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class TrashedProductService
{
    public function __construct(
        protected Repository $config,
    ) {}

    public function getAll(): LengthAwarePaginator
    {
        try {
            $products = QueryBuilder::for(Product::class)
                ->onlyTrashed()
                ->with(['warehouse:id,product_id,total_quantity'])
                ->join('warehouses', 'warehouses.product_id', '=', 'products.id')
                ->defaultSort('-deleted_at')
                ->allowedSorts([
                    AllowedSort::custom('deleted_at', new DeletedAtSort()),
                    AllowedSort::custom('quantity', new TotalQuantitySort()),
                ])
                ->allowedFilters([
                    AllowedFilter::custom('in_stock', new ProductInStockFilter()),
                ])
                ->paginate($this->config->get('product.retrieve.trashed'), [
                    'products.id',
                    'products.title',
                    'products.status',
                    'products.deleted_at',
                    'products.product_article',
                    'preview_image',
                ]);
        } catch (Throwable $e) {
            throw new InvalidFilterSortParamException();
        }

        return $products;
    }
}