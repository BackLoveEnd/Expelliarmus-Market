<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Warehouse\Http\Actions\GetWarehouseProductsPaginatedAction as GetProductAction;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use Modules\Warehouse\Http\Resources\Warehouse\SearchedProductsSetResource;
use Modules\Warehouse\Http\Resources\Warehouse\WarehouseProductInfoResource;
use Modules\Warehouse\Http\Resources\Warehouse\WarehouseProductsTableResource;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class WarehouseController extends Controller
{
    public function __construct(
        private WarehouseProductInfoService $warehouseService,
    ) {
        $this->authorize('view', Warehouse::class);
    }

    /**
     * Search product by article, title, brand. Retrieve only info to show it in autocomplete.
     *
     * Usage - Admin section.
     */
    public function searchProductBySearchable(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $searchedProducts = $this->warehouseService->searchProducts($request->query('search'));

        if ($searchedProducts->isEmpty()) {
            return response()->json(['message' => 'Products by this search key not found.'], 404);
        }

        return SearchedProductsSetResource::collection($searchedProducts);
    }

    /**
     * Show product information in warehouse.
     *
     * Usage - Admin section.
     */
    public function getWarehouseProductInfo(ProductSlug $productSlug): WarehouseProductInfoResource
    {
        $product = $this->warehouseService->getWarehouseInfoAboutProduct($productSlug->getProductId());

        return WarehouseProductInfoResource::make($product);
    }

    /**
     * Retrieve products from warehouse with pagination.
     *
     * Usage - Admin section.
     *
     * @throws InvalidFilterSortParamException
     */
    public function getProductPaginated(Request $request, GetProductAction $action): JsonApiResourceCollection
    {
        if ($request->hasAny(['limit', 'offset'])) {
            $products = $action->handle(
                limit: (int) $request->query('limit', config('warehouse.pagination.table')),
                offset: (int) $request->query('offset', 0),
            );
        } else {
            $products = $action->handle(
                limit: config('warehouse.pagination.table'),
                offset: 0,
            );
        }

        return WarehouseProductsTableResource::collection($products->items)
            ->additional($products->wrapMeta());
    }
}
