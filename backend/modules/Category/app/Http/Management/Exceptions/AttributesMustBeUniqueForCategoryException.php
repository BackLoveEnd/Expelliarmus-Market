<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AttributesMustBeUniqueForCategoryException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Attributes must be unique for category.'], 400);
    }
}
