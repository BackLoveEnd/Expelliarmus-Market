<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Filters\CategoryFilter;
use Modules\Product\Models\Product;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Spatie\QueryBuilder\AllowedFilter;

class RetrieveProductsService
{
    public function __construct(
        private ProductsRetriever $retriever,
        private WarehouseProductInfoService $warehouseService,
    ) {}

    public function getProducts()
    {
        $this->retriever->filtersConnector->defineFilters([
            AllowedFilter::custom('category', new CategoryFilter()),
        ]);

        $this->retriever->sortsConnector->defineSorts([]);

        $products = $this->retriever->retrieve([
            'id',
            'slug',
            'title',
            'preview_image',
        ]);

        $products = $this->warehouseService->getWarehouseInfoAboutProducts(
            products: $products->getCollection(),
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price'], ['name']],
                combinedAttrCols: [['id', 'price'], []],
                warehouseCols: ['id', 'default_price', 'product_id'],
            ),
        );

        dd($products);
    }

    private function mapPricesForProducts(Collection $products): Collection
    {
        return $products->each(function (Product $product) {
            if (is_null($product->hasCombinedAttributes())) {
                $product->price = $product->warehouse->default_price;
            } elseif ($product->hasCombinedAttributes()) {
                $product->price = $product->combinedAttributes->first()->price;
            } else {
                $product->price = $product->singleAttributes->first()->price;
            }
        });
    }
}