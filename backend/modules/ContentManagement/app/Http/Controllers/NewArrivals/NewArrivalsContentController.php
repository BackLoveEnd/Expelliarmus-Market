<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Controllers\NewArrivals;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\ContentManagement\Http\Dto\NewArrivals\ArrivalContentDto;
use Modules\ContentManagement\Http\Exceptions\FailedToDeleteArrivalException;
use Modules\ContentManagement\Http\Requests\NewArrivals\NewArrivalsUploadRequest;
use Modules\ContentManagement\Http\Resources\NewArrivals\NewArrivalsContentResource;
use Modules\ContentManagement\Models\NewArrival;
use Modules\ContentManagement\Services\NewArrivals\NewArrivalsContentService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class NewArrivalsContentController extends Controller
{
    public function __construct(
        private NewArrivalsContentService $service
    ) {
    }

    /**
     * Upload new arrivals.
     *
     * Usage - Admin section.
     *
     * @param  NewArrivalsUploadRequest  $request
     * @return JsonResponse
     */
    public function uploadNewArrivals(NewArrivalsUploadRequest $request): JsonResponse
    {
        $this->service->saveArrivals(
            ArrivalContentDto::collection($request->arrivals)
        );

        return response()->json(['message' => 'New arrivals uploaded successfully.']);
    }

    /**
     * Get new arrivals.
     *
     * Usage - Admin section.
     *
     * @return JsonApiResourceCollection
     */
    public function getNewArrivals(): JsonApiResourceCollection
    {
        return NewArrivalsContentResource::collection($this->service->getArrivals());
    }

    /**
     * Delete arrival.
     *
     * Usage - Admin section.
     *
     * @param  NewArrival  $arrival
     * @return JsonResponse
     * @throws FailedToDeleteArrivalException
     */
    public function deleteArrival(NewArrival $arrival): JsonResponse
    {
        $this->service->deleteArrival($arrival);

        return response()->json(['message' => 'Arrival deleted successfully.']);
    }
}