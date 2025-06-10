<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class VariationToApplyDiscountDoesNotExists extends Exception
{
    public function render(): JsonResponse
    {
        return response()
            ->json(['message' => 'The variation to which the discount was attempted does not exists.'], 404);
    }
}
