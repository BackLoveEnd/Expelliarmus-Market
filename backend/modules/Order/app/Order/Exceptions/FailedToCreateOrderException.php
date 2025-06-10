<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class FailedToCreateOrderException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to create order. Please try again or contact us.'], 500);
    }
}
