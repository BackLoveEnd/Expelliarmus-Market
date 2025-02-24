<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotMovePublishedProductToTrashException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => "Can't move published product to trash."], 403);
    }
}