<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\Actions\Product\Retrieve\GetPreviewProductInformationAction;
use Modules\Product\Http\Management\Actions\Product\Retrieve\GetProductsByCategoryAction as CategoryProducts;
use Modules\Product\Http\Management\Actions\Product\Retrieve\GetProductsByRootCategoryAction as RootCategoryProducts;
use Modules\Product\Http\Management\Actions\Product\Retrieve\GetProductStaffInfoAction as ProductStaffInfo;
use Modules\Product\Http\Management\Resources\Product\ProductPreviewResource;
use Modules\Product\Http\Management\Resources\Product\ProductsPreviewByCategory as Resource;
use Modules\Product\Http\Management\Resources\Product\ProductsPreviewByRootCategories;
use Modules\Product\Http\Management\Resources\Product\ProductStaffInfoResource;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Product\Models\Product;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RetrieveProductController extends Controller
{
    public function __construct(
        private CacheService $cacheService,
    ) {}

    /**
     * Fetch products by all available root categories (paginated).
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getProductsByRootCategories(RootCategoryProducts $action): JsonApiResourceCollection
    {
        $this->authorize('view', Product::class);

        return ProductsPreviewByRootCategories::collection($action->handle());
    }

    /**
     * Fetch products by specific category (paginated). It works as for 'show more'
     * in products by root categories.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getProductsByCategory(Category $category, CategoryProducts $action): JsonResponse|Resource
    {
        $this->authorize('view', Product::class);

        $productsByCategory = $action->handle($category);

        if ($productsByCategory->products->isEmpty()) {
            return response()->json(['message' => 'Products for that category not found.'], 404);
        }

        return Resource::make($action->handle($category));
    }

    /**
     * Show product data on preview page.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function show(ProductSlug $productSlug, GetPreviewProductInformationAction $action): ProductPreviewResource
    {
        $this->authorize('view', Product::class);

        $product = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(
                configKey: config('product.cache.product-preview'),
                identifier: $productSlug->getProductId(),
            ),
            ttl: now()->addMonth(),
            callback: function () use ($productSlug, $action) {
                return $action->handle($productSlug->getProductId());
            },
        );

        return ProductPreviewResource::make($product);
    }

    /**
     * Retrieve all staff info about product for edit.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getProductStaffInfo(ProductSlug $productBind, ProductStaffInfo $action): ProductStaffInfoResource
    {
        $this->authorize('view', Product::class);

        return ProductStaffInfoResource::make($action->handle($productBind->getProductId()));
    }
}
