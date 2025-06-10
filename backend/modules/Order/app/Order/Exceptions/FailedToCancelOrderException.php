<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToCancelOrderException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to cancel order.'], 500);
    }
}
