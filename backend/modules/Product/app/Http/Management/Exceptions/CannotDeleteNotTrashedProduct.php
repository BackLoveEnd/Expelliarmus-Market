<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotDeleteNotTrashedProduct extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Force deleting not trashed product is not allowed.'], 403);
    }
}
