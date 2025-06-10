<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ReachedGlobalCouponUserLimitException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'You have reached the usage limit for this coupon.'], 403);
    }
}
