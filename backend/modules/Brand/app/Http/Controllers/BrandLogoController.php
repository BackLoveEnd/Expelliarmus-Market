<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Brand\Http\Requests\BrandImageUploadRequest;
use Modules\Brand\Models\Brand;
use Modules\Brand\Services\BrandImageService;

class BrandLogoController extends Controller
{
    public function __construct(
        private BrandImageService $imageService,
    ) {}

    /**
     * Upload a brand logo.
     *
     * Usage place - Admin section.
     */
    public function upload(BrandImageUploadRequest $request, Brand $brand): JsonResponse
    {
        $this->imageService->upload($request->file('image'), $brand);

        return response()->json(['message' => 'Logo for brand updated successfully.']);
    }
}
