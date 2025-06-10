<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Category\Http\Shop\Actions\GetCategoriesBrowseListAction as BrowseAction;
use Modules\Category\Http\Shop\Resources\CategoriesBrowseListResource;
use Modules\Category\Http\Shop\Resources\CategoriesChildResource;
use Modules\Category\Models\Category;
use TiMacDonald\JsonApi\JsonApiResourceCollection as JsonApiResponse;

class ShopCategoryAccessController extends Controller
{
    /**
     * Retrieve categories browse list for home page.
     *
     * Usage place - Shop.
     */
    public function getCategoriesBrowseList(Request $request, BrowseAction $action): JsonApiResponse|JsonResponse
    {
        $categories = $action->handle((int) $request->query('limit'));

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found.'], 404);
        }

        return CategoriesBrowseListResource::collection($categories);
    }

    /**
     * Retrieve children of category.
     *
     * Usage place - Shop.
     */
    public function getChildrenOfCategory(Category $category): JsonApiResponse|JsonResponse
    {
        $children = $category->children->load(['parent:id,name,slug', 'children:id,parent_id']);

        if ($children->isEmpty()) {
            return response()->json(['message' => 'Children for requested category not found.'], 404);
        }

        return CategoriesChildResource::collection($children);
    }
}
