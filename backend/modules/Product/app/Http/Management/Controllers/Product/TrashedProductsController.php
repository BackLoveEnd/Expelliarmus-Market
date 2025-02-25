<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Exceptions\CannotDeleteNotTrashedProduct;
use Modules\Product\Http\Management\Exceptions\CannotRestoreNotTrashedProductException;
use Modules\Product\Http\Management\Resources\Product\TrashedProductsResource;
use Modules\Product\Http\Management\Service\Product\TrashedProductService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Http\Exceptions\InvalidFilterSortParamException;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class TrashedProductsController extends Controller
{
    public function __construct(private TrashedProductService $trashedService) {}

    /**
     * Retrieve all trashed products paginated.
     *
     * Usage place - Admin section.
     *
     * @param  Request  $request
     * @return JsonApiResourceCollection|JsonResponse
     * @throws InvalidFilterSortParamException
     */
    public function getTrashed(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $products = $this->trashedService->getAll();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Products not found.'], 404);
        }

        return TrashedProductsResource::collection($products->items())
            ->additional([
                'links' => [
                    'first' => $products->url(1),
                    'next' => $products->nextPageUrl(),
                    'last' => $products->lastPage(),
                    'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                ],
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                ],
            ]);
    }

    /**
     * Restore trashed product.
     *
     * Usage place - Admin section.
     *
     * @param  Product  $product
     * @return JsonResponse
     * @throws CannotRestoreNotTrashedProductException
     */
    public function restore(Product $product): JsonResponse
    {
        $this->trashedService->restore($product);

        return response()->json([
            'message' => 'Product was successfully restored. Status was set to Not Published.',
        ]);
    }

    /**
     * Delete trashed product forever.
     *
     * Usage place - Admin section.
     *
     * @param  Product  $product
     * @return JsonResponse
     * @throws CannotDeleteNotTrashedProduct
     */
    public function deleteForever(Product $product): JsonResponse
    {
        $this->trashedService->deleteForever($product);

        return response()->json(['message' => 'Product was successfully deleted.']);
    }
}