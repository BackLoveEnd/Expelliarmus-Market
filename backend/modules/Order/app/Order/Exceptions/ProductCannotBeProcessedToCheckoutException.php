<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ProductCannotBeProcessedToCheckoutException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Product currently cannot be processed to checkout.'], 422);
    }
}
