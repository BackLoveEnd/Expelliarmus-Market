<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CannotAddDiscountToProductWithoutPriceException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cannot add discount for product with price 0.'], 400);
    }
}
