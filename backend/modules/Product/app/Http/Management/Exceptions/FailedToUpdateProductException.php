<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToUpdateProductException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Failed to update product. Try again or contact us.',
        ], 500);
    }
}
