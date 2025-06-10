<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Http\Controllers\UserController;

// Temporary
Route::get('/dev-login', function () {
    return view('telescope.auth-telescope');
});

// Temporary
Route::post('/dev-login', function (Request $request) {
    $user = Manager::query()
        ->where('email', $request->email)
        ->where('is_super_manager', true)
        ->first();

    if (! $user) {
        return back()->withErrors(['email' => 'User not found.']);
    }

    Auth::guard('manager')->login($user);

    return redirect('/telescope');
});

Route::prefix('api')->group(function () {
    $limiter = config('fortify.limiters.login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(
            array_filter([
                'guest',
                'guest.manager',
                $limiter ? 'throttle:'.$limiter : null,
            ]),
        )->name('login.store');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('logout');

    Route::post('/register', [UserController::class, 'store'])
        ->middleware(['guest'])
        ->name('register.store');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware(['guest'])
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware(['guest'])
        ->name('password.update');
});

Route::middleware('auth:sanctum')->group(function () {
    /*// Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        if ($enableViews) {
            Route::get(
                RoutePath::for('password.request', '/forgot-password'),
                [PasswordResetLinkController::class, 'create']
            )
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('password.request');

            Route::get(
                RoutePath::for('password.reset', '/reset-password/{token}'),
                [NewPasswordController::class, 'create']
            )
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('password.reset');
        }

        Route::post(RoutePath::for('password.email', '/forgot-password'), [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.email');

        Route::post(RoutePath::for('password.update', '/reset-password'), [NewPasswordController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.update');
    }*/
    /*// Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        if ($enableViews) {
            Route::get(
                RoutePath::for('verification.notice', '/email/verify'),
                [EmailVerificationPromptController::class, '__invoke']
            )
                ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
                ->name('verification.notice');
        }

        Route::get(
            RoutePath::for('verification.verify', '/email/verify/{id}/{hash}'),
            [VerifyEmailController::class, '__invoke']
        )
            ->middleware(
                [
                    config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'),
                    'signed',
                    'throttle:'.$verificationLimiter
                ]
            )
            ->name('verification.verify');

        Route::post(
            RoutePath::for('verification.send', '/email/verification-notification'),
            [EmailVerificationNotificationController::class, 'store']
        )
            ->middleware(
                [
                    config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'),
                    'throttle:'.$verificationLimiter
                ]
            )
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put(
            RoutePath::for('user-profile-information.update', '/user/profile-information'),
            [ProfileInformationController::class, 'update']
        )
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put(RoutePath::for('user-password.update', '/user/password'), [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-password.update');
    }

    // Password Confirmation...
    if ($enableViews) {
        Route::get(
            RoutePath::for('password.confirm', '/user/confirm-password'),
            [ConfirmablePasswordController::class, 'show']
        )
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('password.confirm');
    }

    Route::get(
        RoutePath::for('password.confirmation', '/user/confirmed-password-status'),
        [ConfirmedPasswordStatusController::class, 'show']
    )
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirmation');

    Route::post(
        RoutePath::for('password.confirm', '/user/confirm-password'),
        [ConfirmablePasswordController::class, 'store']
    )
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirm.store');

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get(
                RoutePath::for('two-factor.login', '/two-factor-challenge'),
                [TwoFactorAuthenticatedSessionController::class, 'create']
            )
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('two-factor.login');
        }

        Route::post(
            RoutePath::for('two-factor.login', '/two-factor-challenge'),
            [TwoFactorAuthenticatedSessionController::class, 'store']
        )
            ->middleware(
                array_filter([
                    'guest:'.config('fortify.guard'),
                    $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
                ])
            )->name('two-factor.login.store');

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm']
            : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];

        Route::post(
            RoutePath::for('two-factor.enable', '/user/two-factor-authentication'),
            [TwoFactorAuthenticationController::class, 'store']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::post(
            RoutePath::for('two-factor.confirm', '/user/confirmed-two-factor-authentication'),
            [ConfirmedTwoFactorAuthenticationController::class, 'store']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.confirm');

        Route::delete(
            RoutePath::for('two-factor.disable', '/user/two-factor-authentication'),
            [TwoFactorAuthenticationController::class, 'destroy']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get(
            RoutePath::for('two-factor.qr-code', '/user/two-factor-qr-code'),
            [TwoFactorQrCodeController::class, 'show']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get(
            RoutePath::for('two-factor.secret-key', '/user/two-factor-secret-key'),
            [TwoFactorSecretKeyController::class, 'show']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get(
            RoutePath::for('two-factor.recovery-codes', '/user/two-factor-recovery-codes'),
            [RecoveryCodeController::class, 'index']
        )
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post(
            RoutePath::for('two-factor.recovery-codes', '/user/two-factor-recovery-codes'),
            [RecoveryCodeController::class, 'store']
        )
            ->middleware($twoFactorMiddleware);
    }*/
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Expelliarmus Shop API v1.0',
        'status' => 200,
    ]);
});
