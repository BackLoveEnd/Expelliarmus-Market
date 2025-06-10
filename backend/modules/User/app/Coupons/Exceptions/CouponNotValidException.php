<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CouponNotValidException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Coupon is not valid.'], 422);
    }
}
