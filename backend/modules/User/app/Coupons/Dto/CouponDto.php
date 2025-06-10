<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Dto;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Support\Carbon;
use Modules\User\Coupons\Enum\CouponTypeEnum;

final readonly class CouponDto
{
    public function __construct(
        public Carbon $expiresAt,
        public CouponTypeEnum $type,
        public int $discount,
        public ?string $couponCode = null,
        public ?string $email = null,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request): CouponDto
    {
        return new self(
            expiresAt: Carbon::parse($request->expires_at)->setTimezone(config('app.timezone')),
            type: CouponTypeEnum::fromString($request->type),
            discount: (int) $request->discount,
            couponCode: $request->coupon_code,
            email: $request->email,
        );
    }
}
