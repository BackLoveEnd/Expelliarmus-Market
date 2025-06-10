<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class BootstrapExceptionsHelper
{
    public function __construct(
        private Exceptions $exceptions,
    ) {}

    public static function init(Exceptions $exceptions): BootstrapExceptionsHelper
    {
        return new self($exceptions);
    }

    public function defineRules(): BootstrapExceptionsHelper
    {
        $this->renderJsonOnApi();

        return $this;
    }

    public function defineRenderable(): BootstrapExceptionsHelper
    {
        $this->renderNotFound();
        $this->renderMethodNotAllowed();
        $this->renderUnhandleDatabaseException();
        $this->renderAccessDenied();
        $this->renderServiceUnavailableException();

        return $this;
    }

    private function renderServiceUnavailableException(): void
    {
        $this->exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === Response::HTTP_SERVICE_UNAVAILABLE && app()->isDownForMaintenance()) {
                return response()->json([
                    'message' => 'Service unavailable.',
                    'detail' => 'Application is down for maintenance.',
                ], 503);
            }
        });
    }

    private function renderJsonOnApi(): void
    {
        $this->exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    }

    private function renderNotFound(): void
    {
        $this->exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.',
                ], 404);
            }
        });
    }

    private function renderAccessDenied(): void
    {
        $this->exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 403);
            }
        });
    }

    private function renderMethodNotAllowed(): void
    {
        $this->exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Method not allowed',
            ], 405);
        });
    }

    private function renderUnhandleDatabaseException(): void
    {
        $this->exceptions->render(function (QueryException $e, Request $request) {
            if (! app()->environment('local')) {
                return response()->json([
                    'message' => 'Database internal error. Please contact us.',
                ], 500);
            }
        });
    }
}
