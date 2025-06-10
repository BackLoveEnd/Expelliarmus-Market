<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheService;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\Product\Http\Shop\Actions\GetPublicProductInfoAction;
use Modules\Product\Http\Shop\Resources\ProductPublicInfoResource;

class RetrieveProductController extends Controller
{
    public function __construct(
        private GetPublicProductInfoAction $action,
        private CacheService $cacheService,
    ) {}

    /**
     * Retrieve product public info.
     *
     * Usage place - Shop.
     */
    public function __invoke(ProductSlug $productBind): ProductPublicInfoResource
    {
        $product = $this->cacheService->repo()->remember(
            key: $this->cacheService->key(
                configKey: config('product.cache.product-public'),
                identifier: $productBind->getProductId(),
            ),
            ttl: now()->addHours(12),
            callback: fn () => $this->action->handle($productBind->getProductId()),
        );

        return ProductPublicInfoResource::make($product);
    }
}
