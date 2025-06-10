<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToDeleteCategoryAttributeException extends Exception
{
    public static function attributesHasUsageInProducts(): FailedToDeleteCategoryAttributeException
    {
        return new self('Failed to delete attribute: attribute is used in product(s).', 409);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
        ], 409);
    }
}
