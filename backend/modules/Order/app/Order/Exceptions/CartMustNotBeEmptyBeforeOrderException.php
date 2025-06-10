<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CartMustNotBeEmptyBeforeOrderException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Cart must not be empty before order.'], 409);
    }
}
