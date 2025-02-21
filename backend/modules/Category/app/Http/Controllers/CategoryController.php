<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Category\Http\Actions\DeleteCategoryAttributeAction as DeleteAction;
use Modules\Category\Http\Actions\EditCategoryAction;
use Modules\Category\Http\Actions\GetCategoryAttributesAction;
use Modules\Category\Http\Actions\SaveCategoryWithAttributesAction;
use Modules\Category\Http\DTO\CreateCategoryDto;
use Modules\Category\Http\DTO\EditCategoryDto;
use Modules\Category\Http\Exceptions\AttributeNotRelatedToCategoryException;
use Modules\Category\Http\Exceptions\FailedToDeleteCategoryAttributeException;
use Modules\Category\Http\Exceptions\FailedToDeleteCategoryException;
use Modules\Category\Http\Requests\CreateCategoryRequest;
use Modules\Category\Http\Requests\EditCategoryRequest;
use Modules\Category\Http\Resources\RootCategoryResource;
use Modules\Category\Http\Resources\TreeCategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Services\CategoryIconService;
use Modules\Warehouse\Models\ProductAttribute as Attribute;
use Throwable;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CategoryController extends Controller
{
    /**
     * Retrieve all categories in tree view.
     *
     * Usage place - Admin section.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        $categories = Category::getAllCategoriesInTree();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found'], 404);
        }

        return TreeCategoryResource::collection($categories);
    }

    /**
     * Retrieve only parent categories.
     *
     * Usage place - Admin section.
     *
     * @return JsonApiResourceCollection|JsonResponse
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
     * @param  Category  $category
     * @param  GetCategoryAttributesAction  $action
     * @return JsonResponse
     */
    public function getAllAttributesForCategory(Category $category, GetCategoryAttributesAction $action): JsonResponse
    {
        $attributes = $action->handle($category);

        if ($attributes->isEmpty()) {
            return response()->json(['message' => 'Attributes for category not found'], 404);
        }

        return response()->json([
            'data' => $attributes
        ]);
    }

    /**
     * Create category.
     *
     * Usage place - Admin Section.
     *
     * @param  CreateCategoryRequest  $request
     * @param  SaveCategoryWithAttributesAction  $action
     * @return JsonResponse
     */
    public function create(CreateCategoryRequest $request, SaveCategoryWithAttributesAction $action): JsonResponse
    {
        $categoryAttributes = CreateCategoryDto::fromRequest($request);

        $category = $action->handle($categoryAttributes);

        return response()->json([
            'message' => 'Category created',
            'data' => [
                'id' => $category->id,
            ]
        ], 201);
    }

    /**
     * Edit category.
     *
     * Usage place - Admin section.
     *
     * @param  EditCategoryRequest  $request
     * @param  EditCategoryAction  $action
     * @return JsonResponse
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
     * @param  Category  $category
     * @param  CategoryIconService  $categoryIconService
     * @return JsonResponse
     * @throws FailedToDeleteCategoryException
     */
    public function delete(Category $category, CategoryIconService $categoryIconService): JsonResponse
    {
        if ($category->hasProducts()) {
            throw FailedToDeleteCategoryException::categoryHasProducts();
        }

        $categoryIconService->delete($category);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }

    /**
     * Delete attribute related to category.
     *
     * Usage place - Admin section.
     *
     * @param  Category  $category
     * @param  Attribute  $attribute
     * @param  DeleteAction  $action
     * @return JsonResponse
     * @throws AttributeNotRelatedToCategoryException
     * @throws FailedToDeleteCategoryAttributeException
     */
    public function deleteAttribute(Category $category, Attribute $attribute, DeleteAction $action): JsonResponse
    {
        $action->handle($category, $attribute);

        return response()->json(['message' => 'Attribute deleted']);
    }
}
