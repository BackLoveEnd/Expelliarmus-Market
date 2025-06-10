<?php

namespace Modules\User\Users\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Manager\Http\Resources\ManagerResource;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Http\Actions\RegularUsers\GetRegularCustomersAction;
use Modules\User\Users\Http\Resources\UserResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserController extends Controller
{
    /**
     * Retrieve user data.
     *
     * Usage place - Admin/Shop.
     */
    public function user(Request $request): UserResource|ManagerResource|JsonResponse
    {
        if ($request->user('manager') !== null) {
            return new ManagerResource($request->user('manager'));
        }

        if ($request->user('web') !== null) {
            return new UserResource($request->user('web'));
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * User store.
     *
     * Usage place - Shop.
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
     * @throws AuthorizationException
     */
    public function getRegularCustomers(Request $request, GetRegularCustomersAction $action): JsonApiResourceCollection
    {
        $this->authorize('viewUsers', Manager::class);

        $users = $action->handle(
            limit: (int) $request->query('limit', config('user.retrieve.users-table')),
            offset: (int) $request->query('offset'),
        );

        return UserResource::collection($users->items)->additional($users->wrapMeta());
    }
}
