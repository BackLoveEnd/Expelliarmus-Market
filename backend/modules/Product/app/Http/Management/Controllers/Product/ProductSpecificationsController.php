<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\Actions\ProductSpecifications\GetSpecificationsByCategoryAction as GetSpecsAction;
use Modules\Product\Http\Management\Resources\ProductSpecifications\ProductSpecsResource;
use Modules\Product\Models\Product;

class ProductSpecificationsController extends Controller
{
    /**
     * Retrieve product specifications by chosen category. That help in autocompletion
     * when user fill the product specifications.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getSpecsByCategory(Category $category, GetSpecsAction $action): ProductSpecsResource|JsonResponse
    {
        $this->authorize('view', Product::class);

        $specsViewDto = $action->handle($category);

        if ($specsViewDto->isEmpty()) {
            return response()->json(['message' => 'Specifications for this category not found'], 404);
        }

        return ProductSpecsResource::make($specsViewDto);
    }
}
