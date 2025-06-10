<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Resources;

use Illuminate\Http\Request;
use Modules\User\Coupons\Dto\CouponUserDto;
use Modules\User\Users\Models\User;
use TiMacDonald\JsonApi\JsonApiResource;

class CouponResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        if ($this->resource instanceof CouponUserDto) {
            return [
                'coupon' => $this->resource->coupon->coupon_id,
                'discount' => $this->resource->coupon->discount,
                'type' => $this->resource->coupon->type->toString(),
                'expires_at' => $this->resource->coupon->expires_at,
                'user_email' => $this->resource->user instanceof User
                    ? $this->resource->user->email
                    : $this->resource->user,
            ];
        }

        return [
            'coupon' => $this->coupon_id,
            'discount' => $this->discount,
            'type' => $this->type->toString(),
            'expires_at' => $this->expires_at,
        ];
    }

    public function toId(Request $request): string
    {
        return $this->resource instanceof CouponUserDto
            ? (string) $this->resource->coupon->id
            : (string) $this->resource->id;
    }

    public function toType(Request $request): string
    {
        return 'coupons';
    }
}
