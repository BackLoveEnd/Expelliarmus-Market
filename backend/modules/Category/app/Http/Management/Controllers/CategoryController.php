<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Category\Http\Management\Actions\DeleteCategoryAttributeAction as DeleteAction;
use Modules\Category\Http\Management\Actions\EditCategoryAction;
use Modules\Category\Http\Management\Actions\GetCategoryAttributesAction;
use Modules\Category\Http\Management\Actions\SaveCategoryWithAttributesAction;
use Modules\Category\Http\Management\DTO\CreateCategoryDto;
use Modules\Category\Http\Management\DTO\EditCategoryDto;
use Modules\Category\Http\Management\Exceptions\AttributeNotRelatedToCategoryException;
use Modules\Category\Http\Management\Exceptions\FailedToDeleteCategoryAttributeException;
use Modules\Category\Http\Management\Exceptions\FailedToDeleteCategoryException;
use Modules\Category\Http\Management\Requests\CreateCategoryRequest;
use Modules\Category\Http\Management\Requests\EditCategoryRequest;
use Modules\Category\Http\Management\Resources\RootCategoryResource;
use Modules\Category\Http\Management\Resources\TreeCategoryResource;
use Modules\Category\Http\Management\Services\CategoryIconService;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute as Attribute;
use Throwable;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CategoryController extends Controller
{
    /**
     * Retrieve all categories in tree view.
     *
     * Usage place - Admin section.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $categories = Category::getAllCategoriesInTree((int) $request->query('limit'));

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found'], 404);
        }

        return TreeCategoryResource::collection($categories);
    }

    /**
     * Retrieve only parent categories.
     *
     * Usage place - Admin section.
     */
    public function rootCategories(): JsonApiResourceCollection|JsonResponse
    {
        $categories = Category::onlyRoot();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found'], 404);
        }

        return RootCategoryResource::collection($categories);
    }

    /**
     * Retrieve attributes related to category. This attributes used to
     * fulfill information about product attributes, such as color, size etc.
     *
     * Usage place - Admin section.
     *
     *
     * @throws AuthorizationException
     */
    public function getAllAttributesForCategory(
        Category $category,
        GetCategoryAttributesAction $action,
    ): JsonResponse {
        $this->authorize('view', Category::class);

        $attributes = $action->handle($category);

        if ($attributes->isEmpty()) {
            return response()->json(['message' => 'Attributes for category not found'], 404);
        }

        return response()->json([
            'data' => $attributes,
        ]);
    }

    /**
     * Create category.
     *
     * Usage place - Admin Section.
     */
    public function create(
        CreateCategoryRequest $request,
        SaveCategoryWithAttributesAction $action,
    ): JsonResponse {
        $categoryAttributes = CreateCategoryDto::fromRequest($request);

        $category = $action->handle($categoryAttributes);

        return response()->json([
            'message' => 'Category created',
            'data' => [
                'id' => $category->id,
            ],
        ], 201);
    }

    /**
     * Edit category.
     *
     * Usage place - Admin section.
     */
    public function edit(EditCategoryRequest $request, EditCategoryAction $action): JsonResponse
    {
        try {
            $action->handle(EditCategoryDto::fromRequest($request));
        } catch (Throwable $e) {
            return response()->json(['message' => 'Unknown error'], 500);
        }

        return response()->json(['message' => 'Category has been changed successfully']);
    }

    /**
     * Delete category.
     *
     * Usage place - Admin section.
     *
     *
     * @throws FailedToDeleteCategoryException|AuthorizationException
     */
    public function delete(Category $category, CategoryIconService $categoryIconService): JsonResponse
    {
        $this->authorize('manage', Category::class);

        if ($category->hasProducts()) {
            throw FailedToDeleteCategoryException::categoryHasProducts();
        }

        $categoryIconService->delete($category);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted',
        ]);
    }

    /**
     * Delete attribute related to category.
     *
     * Usage place - Admin section.
     *
     *
     * @throws AttributeNotRelatedToCategoryException
     * @throws FailedToDeleteCategoryAttributeException
     */
    public function deleteAttribute(
        Category $category,
        Attribute $attribute,
        DeleteAction $action,
    ): JsonResponse {
        $action->handle($category, $attribute);

        return response()->json(['message' => 'Attribute deleted']);
    }
}
