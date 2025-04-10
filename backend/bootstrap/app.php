<?php

use App\Helpers\BootstrapExceptionsHelper;
use App\Http\Middleware\AcceptApplicationJsonMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Modules\Manager\Http\Middleware\AuthManagerMiddleware;
use Modules\Manager\Http\Middleware\GuestManagerMiddleware;
use Modules\Product\Http\Middleware\AppendIncludeRelationships;
use Modules\User\Http\Exceptions\AlreadyAuthenticatedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->throttleApi();

        $middleware->append(AcceptApplicationJsonMiddleware::class);

        $middleware->alias([
            'include' => AppendIncludeRelationships::class,
            'auth.manager' => AuthManagerMiddleware::class,
            'guest.manager' => GuestManagerMiddleware::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->expectsJson()) {
                throw new AlreadyAuthenticatedException();
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        BootstrapExceptionsHelper::init($exceptions)
            ->defineRenderable()
            ->defineRules();
    })->create();
