<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Warehouse;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Management\Service\Attributes\Handlers\ProductAttributeHandler;
use Modules\Product\Http\Management\Service\Attributes\Handlers\ProductAttributeService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Filters\ProductInStockFilter;
use Modules\Warehouse\Filters\StatusFilter;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use Modules\Warehouse\Sorts\ArrivedAtSort;
use Modules\Warehouse\Sorts\TotalQuantitySort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class WarehouseProductInfoService
{
    public function __construct(
        protected ProductAttributeService $productAttributeService,
    ) {}

    public function searchProducts(mixed $searchable): Collection
    {
        return Product::withTrashed()
            ->search($searchable)
            ->get(['id', 'title', 'product_article']);
    }

    public function getPaginated(int $offset, int $limit): array
    {
        try {
            $products = QueryBuilder::for(Product::class)
                ->defaultSort('title')
                ->join('warehouses', 'warehouses.product_id', '=', 'products.id')
                ->allowedFilters([
                    AllowedFilter::custom('status', new StatusFilter()),
                    AllowedFilter::custom('in_stock', new ProductInStockFilter()),
                    AllowedFilter::trashed(),
                ])
                ->allowedSorts([
                    'title',
                    AllowedSort::custom('total_quantity', new TotalQuantitySort()),
                    AllowedSort::custom('arrived_at', new ArrivedAtSort()),
                ])
                ->offset($offset)
                ->limit($limit)
                ->get([
                    'products.id',
                    'products.title',
                    'products.product_article',
                    'products.status',
                ]);

            return [
                'items' => $products,
                'additional' => [
                    'meta' => [
                        'total' => Product::query()->count(),
                        'limit' => $limit,
                        'offset' => $offset,
                    ],
                ],
            ];
        } catch (QueryException $e) {
            throw new InvalidFilterSortParamException();
        }
    }

    public function getWarehouseInfoAboutProduct(int $productId): Product
    {
        $product = $this->loadProduct($productId);

        if (is_null($product->hasCombinedAttributes())) {
            return $product;
        }

        $product->productAttributes = $this->getProductAttributes(
            product: $product,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [
                    ['id', 'price', 'attribute_id', 'value', 'quantity'],
                    ['id', 'name', 'type'],
                ],
                combinedAttrCols: [
                    ['id', 'sku', 'price', 'quantity'],
                    ['product_attributes.id', 'name', 'type'],
                ],
            ),
        );

        return $product;
    }

    private function loadProduct(int $productId): Product
    {
        return Product::withTrashed()->where('id', $productId)
            ->with([
                'category:id,name',
                'brand:id,name',
                'warehouse',
            ])
            ->firstOrFail([
                'id',
                'title',
                'preview_image',
                'category_id',
                'brand_id',
                'product_article',
                'status',
                'with_attribute_combinations',
            ]);
    }

    public function getProductAttributes(Product $product, FetchAttributesColumnsDto $dto): BaseCollection
    {
        $this->productAttributeService->setProduct($product);

        if ($product->hasCombinedAttributes()) {
            return $this->getCombinedAttributesProduct($dto->combinedAttrCols);
        }

        return $this->getSingleAttributesProduct($dto->singleAttrCols);
    }

    public function getAttributeServiceHandler(): ProductAttributeHandler
    {
        return $this->productAttributeService->getAttributeHandler();
    }

    private function getSingleAttributesProduct(array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->getAttributes();
    }

    private function getCombinedAttributesProduct(array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->getAttributes();
    }
}