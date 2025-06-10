<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FailedToUpdateCouponException extends Exception
{
    public function report(): false
    {
        return false;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Failed to update coupon.'], 500);
    }
}
