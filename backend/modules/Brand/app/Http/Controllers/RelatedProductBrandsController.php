<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Brand\Http\Actions\GetProductBrandsByCategoryAction as BrandsProductsAction;
use Modules\Brand\Http\Resources\BrandResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RelatedProductBrandsController extends Controller
{
    /**
     * Retrieve brands for products by products categories.
     *
     * Usage place - Shop.
     */
    public function getProductBrandsByCategory(
        string|int $category,
        BrandsProductsAction $action,
    ): JsonApiResourceCollection|JsonResponse {
        $brands = $action->handle($category);

        if ($brands->isEmpty()) {
            return response()->json(['message' => 'Brands not found.'], 404);
        }

        return BrandResource::collection($brands);
    }
}
