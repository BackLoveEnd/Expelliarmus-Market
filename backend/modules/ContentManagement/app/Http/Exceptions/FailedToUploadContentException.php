<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToUploadContentException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to upload slider content. Please contact us.'], 500);
    }
}
