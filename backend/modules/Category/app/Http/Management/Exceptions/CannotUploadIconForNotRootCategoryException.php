<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotUploadIconForNotRootCategoryException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Adding icons to not root category is not allowed.',
        ], 403);
    }
}
