<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Modules\Manager\Models\Manager;
use Modules\User\Http\Actions\Guests\GetGuestsAction;
use Modules\User\Http\Resources\GuestResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class GuestsController extends Controller
{
    /**
     * Retrieve all guests with pagination.
     *
     * Usage place - Admin section.
     *
     * @param  Request  $request
     * @param  GetGuestsAction  $action
     * @return JsonApiResourceCollection
     * @throws AuthorizationException
     */
    public function getGuests(Request $request, GetGuestsAction $action): JsonApiResourceCollection
    {
        $this->authorize('viewUsers', Manager::class);

        $guests = $action->handle(
            limit: (int)$request->query('limit', config('user.retrieve.users-table')),
            offset: (int)$request->query('offset'),
        );

        return GuestResource::collection($guests->items)->additional($guests->wrapMeta());
    }
}