<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Http\Shop\Actions\GetCategoriesForBrandAction as BrandCategoriesAction;
use Modules\Category\Http\Shop\Resources\CategoriesBrowseListResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CategoriesBrandController extends Controller
{
    /**
     * Get the categories for a specific brand.
     *
     * Usage place - Shop.
     */
    public function getCategoriesForBrand(
        string|int $brandId,
        BrandCategoriesAction $action,
    ): JsonApiResourceCollection|JsonResponse {
        $categories = $action->handle($brandId);

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found.'], 404);
        }

        return CategoriesBrowseListResource::collection($categories);
    }
}
