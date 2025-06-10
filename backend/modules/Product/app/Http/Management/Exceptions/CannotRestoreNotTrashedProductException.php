<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotRestoreNotTrashedProductException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cannot restore not trashed product.'], 409);
    }
}
