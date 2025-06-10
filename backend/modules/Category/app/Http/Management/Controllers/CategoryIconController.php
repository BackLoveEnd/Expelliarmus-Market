<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Http\Management\Exceptions\CannotUploadIconForNotRootCategoryException;
use Modules\Category\Http\Management\Requests\CreateCategoryIconRequest;
use Modules\Category\Http\Management\Services\CategoryIconService;
use Modules\Category\Models\Category;

class CategoryIconController extends Controller
{
    public function __construct(
        private CategoryIconService $categoryIconService
    ) {}

    /**
     * Upload icon for category.
     *
     * Usage place - Admin section.
     *
     *
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
     *
     * @throws CannotUploadIconForNotRootCategoryException
     */
    public function editIcon(CreateCategoryIconRequest $request, Category $category): JsonResponse
    {
        $this->categoryIconService->edit($request->file('icon'), $category);

        return response()->json(['message' => 'Icon updated successfully.']);
    }
}
