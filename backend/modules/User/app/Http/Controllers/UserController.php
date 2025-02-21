<?php

namespace Modules\User\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Resources\UserResource;

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
}