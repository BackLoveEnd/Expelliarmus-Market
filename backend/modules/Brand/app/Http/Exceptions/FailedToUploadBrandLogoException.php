<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToUploadBrandLogoException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to upload brand logo.'], 500);
    }
}
