<?php

declare(strict_types=1);

namespace Modules\Manager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Manager\Http\Actions\GetManagersPaginatedAction as ManagersAction;
use Modules\Manager\Http\Resources\ManagerResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class RetrieveManagersController extends Controller
{
    /**
     * Retrieve managers paginated.
     *
     * Usage place - Admin section.
     *
     * @param  Request  $request
     * @param  ManagersAction  $action
     * @return JsonApiResourceCollection|JsonResponse
     */
    public function retrieveManagerTable(
        Request $request,
        ManagersAction $action,
    ): JsonApiResourceCollection|JsonResponse {
        $dto = $action->handle(
            limit: (int)$request->query('limit', config('manager.retrieve.table')),
            offset: (int)$request->query('offset', 0),
        );

        if ($dto->items->isEmpty()) {
            return response()->json(['message' => 'Managers not found.'], 404);
        }

        return ManagerResource::collection($dto->items)->additional($dto->wrapMeta());
    }
}