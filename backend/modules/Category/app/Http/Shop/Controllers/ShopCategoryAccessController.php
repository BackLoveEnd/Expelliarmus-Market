<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Http\Shop\Actions\GetCategoriesBrowseListAction as BrowseAction;
use Modules\Category\Http\Shop\Resources\CategoriesBrowseListResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class ShopCategoryAccessController extends Controller
{

    /**
     * Retrieve categories browse list for home page.
     *
     * Usage place - Shop.
     *
     * @param  \Modules\Category\Http\Shop\Actions\GetCategoriesBrowseListAction  $action
     *
     * @return \TiMacDonald\JsonApi\JsonApiResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getCategoriesBrowseList(BrowseAction $action): JsonApiResourceCollection|JsonResponse
    {
        $categories = $action->handle();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found.'], 404);
        }

        return CategoriesBrowseListResource::collection($action->handle());
    }

}
