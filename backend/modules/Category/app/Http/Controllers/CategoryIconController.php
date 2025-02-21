<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Http\Exceptions\CannotUploadIconForNotRootCategoryException;
use Modules\Category\Http\Requests\CreateCategoryIconRequest;
use Modules\Category\Models\Category;
use Modules\Category\Services\CategoryIconService;

class CategoryIconController extends Controller
{
    public function __construct(
        private CategoryIconService $categoryIconService
    ) {
    }

    /**
     * Upload icon for category.
     *
     * Usage place - Admin section.
     *
     * @param  CreateCategoryIconRequest  $request
     * @param  Category  $category
     * @return JsonResponse
     * @throws CannotUploadIconForNotRootCategoryException
     */
    public function uploadIcon(CreateCategoryIconRequest $request, Category $category): JsonResponse
    {
        $this->categoryIconService->upload($request->file('icon'), $category);

        return response()->json(['message' => 'Icon uploaded successfully.']);
    }

    /**
     * Edit category icon.
     *
     * Usage place - Admin section.
     *
     * @param  CreateCategoryIconRequest  $request
     * @param  Category  $category
     * @return JsonResponse
     * @throws CannotUploadIconForNotRootCategoryException
     */
    public function editIcon(CreateCategoryIconRequest $request, Category $category): JsonResponse
    {
        $this->categoryIconService->edit($request->file('icon'), $category);

        return response()->json(['message' => 'Icon updated successfully.']);
    }
}