<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotUnpublishTrashedProductException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cannot unpublish trashed product.'], 403);
    }
}
