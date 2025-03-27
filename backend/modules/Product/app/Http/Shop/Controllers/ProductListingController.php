<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Services\Cache\CacheService;
use Illuminate\Http\JsonResponse;
use Modules\Category\Models\Category;
use Modules\Product\Http\Shop\Actions\GetExploredProductsAction;
use Modules\Product\Http\Shop\Actions\GetRelatedToProductProductsAction;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadExploreProductsException;
use Modules\Product\Http\Shop\Resources\ExploredProductsResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class ProductListingController
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Retrieve set of product to show in explore section.
     *
     * Usage place - Shop.
     *
     * @param  GetExploredProductsAction  $action
     * @return JsonApiResourceCollection|JsonResponse
     * @throws FailedToLoadExploreProductsException
     */
    public function explore(GetExploredProductsAction $action): JsonApiResourceCollection|JsonResponse
    {
        $products = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(config('product.cache.products-explore')),
            ttl: now()->addDay(),
            callback: fn() => $action->handle(config('product.retrieve.explore')),
        );

        if (! $products) {
            return response()->json(['message' => 'Products not found.'], 404);
        }

        return ExploredProductsResource::collection($products);
    }

    public function relatedToProduct(Category $category, GetRelatedToProductProductsAction $action) {}
}