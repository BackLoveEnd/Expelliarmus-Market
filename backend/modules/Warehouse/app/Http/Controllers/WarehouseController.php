<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use Modules\Warehouse\Http\Resources\SearchedProductsSetResource;
use Modules\Warehouse\Http\Resources\WarehouseProductInfoResource;
use Modules\Warehouse\Http\Resources\WarehouseProductsTableResource;
use Modules\Warehouse\Services\WarehouseProductInfoService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class WarehouseController extends Controller
{
    public function __construct(
        private WarehouseProductInfoService $warehouseService,
        private CacheService $cacheService,
    ) {}

    /**
     * Search product by article, title, brand. Retrieve only info to show it in autocomplete.
     *
     * Usage - Admin section.
     *
     * @param  Request  $request
     * @return JsonApiResourceCollection|JsonResponse
     */
    public function searchProductBySearchable(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $searchedProducts = $this->warehouseService->searchProducts($request->query('search'));

        if ($searchedProducts->isEmpty()) {
            return response()->json(['message' => "Products by this search key not found."], 404);
        }

        return SearchedProductsSetResource::collection($searchedProducts);
    }

    /**
     * Show product information in warehouse.
     *
     * Usage - Admin section.
     *
     * @param  ProductSlug  $productSlug
     * @return WarehouseProductInfoResource
     */
    public function getWarehouseProductInfo(ProductSlug $productSlug): WarehouseProductInfoResource
    {
        $product = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(
                config('warehouse.cache.product-warehouse-info'),
                $productSlug->getProductId(),
            ),
            ttl: now()->addDay(),
            callback: fn() => $this->warehouseService->getWarehouseInfoAboutProduct($productSlug->getProductId()),
        );

        return WarehouseProductInfoResource::make($product);
    }

    /**
     * Retrieve products from warehouse with pagination.
     *
     * Usage - Admin section.
     *
     * @param  Request  $request
     * @return JsonApiResourceCollection
     * @throws InvalidFilterSortParamException
     */
    public function getProductPaginated(Request $request): JsonApiResourceCollection
    {
        if ($request->hasAny(['limit', 'offset'])) {
            $products = $this->warehouseService->getPaginated(
                offset: (int)$request->query('offset') ?? 0,
                limit: (int)$request->query('limit') ?? config('warehouse.pagination.table'),
            );
        } else {
            $products = $this->warehouseService->getPaginated(
                offset: 0,
                limit: config('warehouse.pagination.table'),
            );
        }

        return WarehouseProductsTableResource::collection($products['items'])
            ->additional($products['additional']);
    }
}