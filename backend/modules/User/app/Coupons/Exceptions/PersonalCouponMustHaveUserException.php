<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PersonalCouponMustHaveUserException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Personal coupon must have user.'], 422);
    }
}
