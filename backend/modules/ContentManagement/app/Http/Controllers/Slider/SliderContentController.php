<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Controllers\Slider;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Modules\ContentManagement\Http\Dto\Slider\SliderContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToDeleteSlideException;
use Modules\ContentManagement\Http\Requests\Slider\UploadSliderContentRequest;
use Modules\ContentManagement\Http\Resources\Slider\SliderContentResource;
use Modules\ContentManagement\Models\ContentSlider;
use Modules\ContentManagement\Services\Slider\SliderContentService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class SliderContentController extends Controller
{
    public function __construct(
        private SliderContentService $contentService,
    ) {}

    /**
     * Allow to upload, edit, delete slider content.
     *
     * Usage - Admin section.
     */
    public function upload(UploadSliderContentRequest $request): JsonResponse
    {
        $this->contentService->saveSlider(
            SliderContentDto::collection($request->images),
        );

        return response()->json(['message' => 'Slider content was uploaded successfully.']);
    }

    /**
     * Retrieve all slider content.
     *
     * Usage - Admin|Shop sections.
     */
    public function getAllSliderContent(): JsonApiResourceCollection
    {
        return SliderContentResource::collection($this->contentService->getAll());
    }

    /**
     * Delete slide from slider content.
     *
     * Usage - Admin section.
     *
     * @throws FailedToDeleteSlideException|AuthorizationException
     */
    public function deleteSlide(ContentSlider $slide): JsonResponse
    {
        $this->authorize('manage', ContentSlider::class);

        $this->contentService->delete($slide);

        return response()->json(['message' => 'Slide was successfully delete.']);
    }
}
