<?php

namespace Modules\User\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Actions\RegularUsers\GetRegularCustomersAction;
use Modules\User\Http\Resources\UserResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserController extends Controller
{
    /**
     * Retrieve user data.
     *
     * Usage place - Admin/Shop.
     *
     * @param  Request  $request
     * @return UserResource
     */
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * User store.
     *
     * Usage place - Shop.
     *
     * @param  Request  $request
     * @param  CreateNewUser  $action
     * @return JsonResponse
     */
    public function store(Request $request, CreateNewUser $action): JsonResponse
    {
        $user = $action->create($request->all());

        event(new Registered($user));

        return response()->json(status: 201);
    }

    /**
     * Get all regular (registered) customers paginated.
     *
     * Usage place - Admin section.
     *
     * @param  Request  $request
     * @param  GetRegularCustomersAction  $action
     * @return JsonApiResourceCollection
     */
    public function getRegularCustomers(Request $request, GetRegularCustomersAction $action): JsonApiResourceCollection
    {
        $users = $action->handle(
            limit: (int) $request->query('limit', config('user.retrieve.users-table')),
            offset: (int) $request->query('offset'),
        );

        return UserResource::collection($users['items'])->additional($users['additional']);
    }
}