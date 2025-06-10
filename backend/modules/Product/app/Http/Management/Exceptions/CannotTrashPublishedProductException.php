<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotTrashPublishedProductException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Moving published product to trash is not allowed.'], 403);
    }
}
