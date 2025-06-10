<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ProductCannotBeAddedToCartException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Product cannot be added to cart. Possibly not in stock.'], 400);
    }
}
