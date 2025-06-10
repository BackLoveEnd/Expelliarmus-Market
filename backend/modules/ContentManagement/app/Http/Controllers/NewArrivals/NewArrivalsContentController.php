<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Controllers\NewArrivals;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
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
        private NewArrivalsContentService $service,
    ) {}

    /**
     * Upload new arrivals.
     *
     * Usage - Admin section.
     */
    public function uploadNewArrivals(NewArrivalsUploadRequest $request): JsonResponse
    {
        $this->service->saveArrivals(
            ArrivalContentDto::collection($request->arrivals),
        );

        return response()->json(['message' => 'New arrivals uploaded successfully.']);
    }

    /**
     * Get new arrivals.
     *
     * Usage - Admin section.
     */
    public function getNewArrivals(): JsonApiResourceCollection|JsonResponse
    {
        $arrivals = $this->service->getArrivals();

        if ($arrivals->isEmpty()) {
            return response()->json(['message' => 'No arrivals found.'], 404);
        }

        return NewArrivalsContentResource::collection($arrivals);
    }

    /**
     * Delete arrival.
     *
     * Usage - Admin section.
     *
     * @throws FailedToDeleteArrivalException|AuthorizationException
     */
    public function deleteArrival(NewArrival $arrival): JsonResponse
    {
        $this->authorize('manage', NewArrival::class);

        $this->service->deleteArrival($arrival);

        return response()->json(['message' => 'Arrival deleted successfully.']);
    }
}
