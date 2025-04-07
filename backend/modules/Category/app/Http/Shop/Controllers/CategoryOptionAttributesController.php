<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheService;
use Modules\Category\Http\Shop\Actions\GetAttributesValuesForCategoryAction as GetAttributesAction;
use Modules\Category\Http\Shop\Resources\CategoryAttributesValuesResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CategoryOptionAttributesController extends Controller
{
    public function __construct(
        private CacheService $cacheService,
    ) {}

    /**
     * Retrieve all attributes for category with their values.
     *
     * Usage place - Shop.
     *
     * @param  int  $categoryId
     * @param  GetAttributesAction  $action
     * @return JsonApiResourceCollection
     */
    public function getOptionAttributesForCategory(
        int $categoryId,
        GetAttributesAction $action,
    ): JsonApiResourceCollection {
        $attributes = $this->cacheService->repo()->remember(
            key: $this->cacheService->key("category:%s:attributes", $categoryId),
            ttl: now()->addWeek(),
            callback: fn() => $action->handle($categoryId),
        );

        return CategoryAttributesValuesResource::collection($attributes);
    }
}