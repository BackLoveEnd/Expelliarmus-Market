<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToDeleteBrandException extends Exception
{
    public static function brandHasProducts(): FailedToDeleteBrandException
    {
        return new self('Failed to delete brand: brand has products.');
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->message], 409);
    }
}
