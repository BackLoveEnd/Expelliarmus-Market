<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotPublishTrashedProductException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cannot publish trashed product.'], 403);
    }
}
