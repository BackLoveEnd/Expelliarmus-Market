<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Actions\Product\Edit\MoveProductToTrashAction;
use Modules\Product\Http\Management\Exceptions\CannotDeleteNotTrashedProduct;
use Modules\Product\Http\Management\Exceptions\CannotRestoreNotTrashedProductException;
use Modules\Product\Http\Management\Exceptions\CannotTrashPublishedProductException;
use Modules\Product\Http\Management\Resources\Product\TrashedProductsResource;
use Modules\Product\Http\Management\Service\Product\TrashedProductService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum as Status;
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
     * @throws InvalidFilterSortParamException|AuthorizationException
     */
    public function getTrashed(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $this->authorize('view', Product::class);

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
     * Move product to trash.
     *
     * Usage place - Admin section.
     *
     * @throws CannotTrashPublishedProductException
     * @throws AuthorizationException
     */
    public function moveToTrash(Product $product, MoveProductToTrashAction $action): JsonResponse
    {
        $this->authorize('lightDelete', Product::class);

        if ($product->trashed() || $product->status->is(Status::TRASHED)) {
            return response()->json(['message' => 'Product is already in trash.'], 409);
        }

        $action->handle($product);

        return response()->json(['message' => 'Product was moved to trash.']);
    }

    /**
     * Restore trashed product.
     *
     * Usage place - Admin section.
     *
     * @throws CannotRestoreNotTrashedProductException
     * @throws AuthorizationException
     */
    public function restore(Product $product): JsonResponse
    {
        $this->authorize('manage', Product::class);

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
     * @throws CannotDeleteNotTrashedProduct
     * @throws AuthorizationException
     */
    public function deleteForever(Product $product): JsonResponse
    {
        $this->authorize('forceDelete', Product::class);

        $this->trashedService->deleteForever($product);

        return response()->json(['message' => 'Product was successfully deleted.']);
    }
}
