<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AttributeNotRelatedToCategoryException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Attribute must be in relation with category'], 404);
    }
}
