<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Dto;

use App\Services\Validators\JsonApiFormRequest;
use Carbon\Carbon;
use Modules\User\Coupons\Models\Coupon;

final readonly class CouponEditDto
{
    public function __construct(
        public Coupon $coupon,
        public Carbon $expiresAt,
        public int $discount,
        public ?string $email = null,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request, Coupon $coupon): CouponEditDto
    {
        return new self(
            coupon: $coupon,
            expiresAt: Carbon::parse($request->expires_at)->setTimezone(config('app.timezone')),
            discount: $request->discount,
            email: $request->email ?? null,
        );
    }
}
