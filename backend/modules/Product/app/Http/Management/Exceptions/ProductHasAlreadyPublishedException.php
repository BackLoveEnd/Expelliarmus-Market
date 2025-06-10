<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ProductHasAlreadyPublishedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Product is already published.']);
    }
}
