<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToLoadRelatedProductsException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to load related products'], 404);
    }
}
