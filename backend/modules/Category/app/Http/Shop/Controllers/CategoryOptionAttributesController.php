<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Cache\CacheService;
use Modules\Category\Http\Shop\Actions\GetAttributesValuesForCategoryAction as GetAttributesAction;
use Modules\Category\Http\Shop\Resources\CategoryAttributesValuesResource;
use Modules\Category\Models\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     */
    public function getOptionAttributesForCategory(
        string $categorySlug,
        GetAttributesAction $action,
    ): JsonApiResourceCollection|JsonResponse {
        $category = Category::query()->whereSlug($categorySlug)->firstOrFail();

        $attributes = $this->cacheService->repo()->remember(
            key: $this->cacheService->key('category:%s:attributes', $category->id),
            ttl: now()->addWeek(),
            callback: fn () => $action->handle($category),
        );

        if ($attributes->isEmpty()) {
            return response()->json(['message' => 'No options attributes found.'], 404);
        }

        return CategoryAttributesValuesResource::collection($attributes);
    }
}
