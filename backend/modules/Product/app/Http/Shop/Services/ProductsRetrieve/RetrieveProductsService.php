<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Filters\CategoryFilter;
use Modules\Product\Http\Shop\Filters\MaxPriceViewFilter;
use Modules\Product\Http\Shop\Filters\MinPriceViewFilter;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Spatie\QueryBuilder\AllowedFilter;

class RetrieveProductsService
{

    public function __construct(
        private ProductsRetriever $retriever,
        private WarehouseProductInfoService $warehouseService,
        private DiscountedProductsService $discountedService,
    ) {}

    public function getProducts(int $retrieveNum): LengthAwarePaginator
    {
        $this->retriever->filtersConnector->defineFilters([
            AllowedFilter::custom('category', new CategoryFilter()),
            AllowedFilter::exact('brand', 'brand_id'),
            AllowedFilter::custom('min_price', new MinPriceViewFilter()),
            AllowedFilter::custom('max_price', new MaxPriceViewFilter()),
        ]);

        $this->retriever->sortsConnector->defineSorts([]);

        $products = $this->retriever->retrieve($retrieveNum, [
            'id',
            'slug',
            'title',
            'preview_image',
        ]);

        return $products->setCollection(
            $this->discountedService->loadDiscountsForProducts(
                $this->loadWarehouseInfo($products->getCollection()),
            ),
        );
    }

    private function loadWarehouseInfo(Collection $products): Collection
    {
        return $this->warehouseService->getWarehouseInfoAboutProducts(
            products: $products,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price'], []],
                combinedAttrCols: [['id', 'price'], []],
                warehouseCols: ['id', 'default_price', 'product_id'],
            ),
        );
    }

}
