<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Filters\CategoryFilter;
use Modules\Product\Http\Shop\Filters\OptionsAttributesFilter;
use Modules\Product\Http\Shop\Filters\PriceViewFilter;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

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
            AllowedFilter::custom('category', new CategoryFilter),
            AllowedFilter::exact('brand', 'brand_id'),
            AllowedFilter::custom('price', new PriceViewFilter),
            AllowedFilter::custom('options', new OptionsAttributesFilter),
        ]);

        $this->retriever->sortsConnector->defineSorts([
            AllowedSort::field('price', 'product_min_prices.min_price'),
        ]);

        $products = $this->retriever->retrieve($retrieveNum, [
            'products.id',
            'products.slug',
            'products.title',
            'products.preview_image',
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
