<?php

use App\Helpers\BootstrapExceptionsHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
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
