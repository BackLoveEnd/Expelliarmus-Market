<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToDeleteCategoryException extends Exception
{
    public static function categoryHasProducts(): FailedToDeleteCategoryException
    {
        return new self('Failed to delete category: category has products.');
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->message], 409);
    }
}
