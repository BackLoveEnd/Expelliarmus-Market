<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DiscountIsNotRelatedToProductException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Discount is not related to product.'], 409);
    }
}
