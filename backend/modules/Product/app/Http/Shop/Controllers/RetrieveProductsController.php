<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
}
