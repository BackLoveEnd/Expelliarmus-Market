<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidDiscountStatusException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Invalid discount status given.'], 404);
    }
}
