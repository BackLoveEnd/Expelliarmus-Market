<?php

declare(strict_types=1);

namespace Modules\Manager\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Manager\Http\Dto\ManagerRegisterDto;
use Modules\Manager\Http\Requests\ManagerLoginRequest;
use Modules\Manager\Http\Requests\ManagerRegisterRequest;
use Modules\Manager\Services\ManagerAuthService;

class ManagerAuthController extends Controller
{
    public function __construct(
        private ManagerAuthService $authService,
    ) {}

    /**
     * Login a manager.
     *
     * Usage place - Admin section.
     */
    public function login(ManagerLoginRequest $request): JsonResponse
    {
        $manager = $this->authService->prepareToLogin(
            (object) [
                'email' => $request->email,
                'password' => $request->password,
            ],
        );

        Auth::guard('manager')->login($manager);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
        ]);
    }

    /**
     * Logout a manager.
     *
     * Usage place - Admin section.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('manager')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Register a manager.
     *
     * Usage place - Admin section.
     */
    public function register(ManagerRegisterRequest $request): JsonResponse
    {
        $this->authService->register(ManagerRegisterDto::fromRequest($request));

        return response()->json([
            'message' => 'Manager was registered successfully and will receive an email shortly.',
        ], 201);
    }
}
