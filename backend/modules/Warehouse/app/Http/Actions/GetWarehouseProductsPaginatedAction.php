<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Database\QueryException;
use Modules\Product\Models\Product;
use Modules\Warehouse\Filters\ProductInStockFilter;
use Modules\Warehouse\Filters\StatusFilter;
use Modules\Warehouse\Filters\WarehouseStatusFilter;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use Modules\Warehouse\Sorts\ArrivedAtSort;
use Modules\Warehouse\Sorts\TotalQuantitySort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class GetWarehouseProductsPaginatedAction
{
    public function handle(int $limit, int $offset): LimitOffsetDto
    {
        try {
            $products = QueryBuilder::for(Product::class)
                ->defaultSort('title')
                ->join('warehouses', 'warehouses.product_id', '=', 'products.id')
                ->allowedFilters([
                    AllowedFilter::custom('status', new StatusFilter),
                    AllowedFilter::custom('warehouse_status', new WarehouseStatusFilter),
                    AllowedFilter::custom('in_stock', new ProductInStockFilter),
                    AllowedFilter::trashed(),
                ])
                ->allowedSorts([
                    'title',
                    AllowedSort::custom('total_quantity', new TotalQuantitySort),
                    AllowedSort::custom('arrived_at', new ArrivedAtSort),
                ])
                ->offset($offset)
                ->limit($limit)
                ->get([
                    'products.id',
                    'products.title',
                    'products.product_article',
                    'products.status',
                    'warehouses.status as warehouse_status',
                    'warehouses.total_quantity',
                    'warehouses.arrived_at',
                ]);

            return new LimitOffsetDto(
                items: $products,
                total: Product::query()->count(),
                limit: $limit,
                offset: $offset,
            );
        } catch (QueryException $e) {
            throw new InvalidFilterSortParamException;
        }
    }
}
