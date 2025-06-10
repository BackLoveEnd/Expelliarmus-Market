<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AlreadyAuthenticatedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => __('Already authenticated')], 403);
    }
}
