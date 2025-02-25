<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Resources\Product\TrashedProductsResource;
use Modules\Product\Http\Management\Service\Product\TrashedProductService;
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
                ],
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                ],
            ]);
    }
}