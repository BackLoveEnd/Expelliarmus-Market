<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
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
     * @return JsonApiResourceCollection
     */
    public function index(): JsonApiResourceCollection
    {
        $products = $this->productsService->getProducts(config('product.retrieve.shop.default'));

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