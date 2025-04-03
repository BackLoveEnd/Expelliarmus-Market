<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Services\Cache\CacheService;
use Illuminate\Http\JsonResponse;
use Modules\Category\Models\Category;
use Modules\Product\Http\Shop\Actions\GetExploredProductsAction;
use Modules\Product\Http\Shop\Actions\GetRelatedToProductProductsAction as RelatedProductsAction;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadExploreProductsException;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadRelatedProductsException;
use Modules\Product\Http\Shop\Resources\ExploredProductsResource;
use Modules\Product\Http\Shop\Resources\ProductsShopCardResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection as JsonApiResource;

class ProductListingController
{
    public function __construct(private CacheService $cacheService) {}

    /**
     * Retrieve set of product to show in explore section.
     *
     * Usage place - Shop.
     *
     * @param  GetExploredProductsAction  $action
     * @return JsonApiResource|JsonResponse
     * @throws FailedToLoadExploreProductsException
     */
    public function explore(GetExploredProductsAction $action): JsonApiResource|JsonResponse
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

    /**
     * Retrieve suggestions.
     *
     * Usage place - Shop.
     *
     * @param  GetExploredProductsAction  $action
     * @return JsonApiResource|JsonResponse
     * @throws FailedToLoadExploreProductsException
     */
    public function suggestions(GetExploredProductsAction $action): JsonApiResource|JsonResponse
    {
        $products = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(config('product.cache.products-suggestions')),
            ttl: now()->addDay(),
            callback: fn() => $action->handle(config('product.retrieve.suggestions')),
        );

        if (! $products) {
            return response()->json(['message' => 'Products not found.'], 404);
        }

        return ExploredProductsResource::collection($products);
    }

    /**
     * Retrieve related products for product by category.
     *
     * Usage place - Shop.
     *
     * @param  Category  $category
     * @param  RelatedProductsAction  $action
     * @return JsonApiResource|JsonResponse
     * @throws FailedToLoadRelatedProductsException
     */
    public function relatedToProduct(Category $category, RelatedProductsAction $action): JsonApiResource|JsonResponse
    {
        $products = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(config('product.cache.product-related-by-category'), $category->id),
            ttl: now()->addDay(),
            callback: fn() => $action->handle($category),
        );

        if (! $products) {
            return response()->json(['message' => 'Products not found.'], 404);
        }

        return ProductsShopCardResource::collection($products);
    }
}