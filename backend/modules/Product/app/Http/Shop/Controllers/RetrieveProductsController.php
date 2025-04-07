<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Shop\Actions\GetMinMaxProductsPriceAction;
use Modules\Product\Http\Shop\Resources\ProductsShopCardResource;
use Modules\Product\Http\Shop\Services\ProductsRetrieve\RetrieveProductsService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RetrieveProductsController extends Controller
{

    public function __construct(
        private RetrieveProductsService $productsService,
    ) {}

    /**
     * Retrieve products shop cards info with filters, sorts.
     *
     * Usage place - Shop.
     *
     * @return JsonApiResourceCollection|JsonResponse
     */
    public function index(): JsonApiResourceCollection|JsonResponse
    {
        $products = $this->productsService->getProducts(config('product.retrieve.shop.default'));

        if (collect($products->items())->isEmpty()) {
            return response()->json(['message' => 'Products not found.'], 404);
        }

        return ProductsShopCardResource::collection($products->items())
            ->additional([
                'links' => [
                    'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                ],
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                ],
            ]);
    }

    /**
     * Retrieve min and max prices of all products.
     *
     * Usage place - Shop.
     *
     * @param  GetMinMaxProductsPriceAction  $action
     * @return JsonResponse
     */
    public function getMinMaxProductsPrice(GetMinMaxProductsPriceAction $action): JsonResponse
    {
        $prices = $action->handle();

        return response()->json([
            'data' => [
                'type' => 'staff',
                'attributes' => [
                    'min_price' => $prices?->min_price ?? 0,
                    'max_price' => $prices?->max_price ?? 0,
                ],
            ],
        ]);
    }
}
