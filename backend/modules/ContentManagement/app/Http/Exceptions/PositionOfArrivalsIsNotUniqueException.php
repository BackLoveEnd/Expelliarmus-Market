<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PositionOfArrivalsIsNotUniqueException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Position of arrivals is not unique.',
        ], 409);
    }
}
