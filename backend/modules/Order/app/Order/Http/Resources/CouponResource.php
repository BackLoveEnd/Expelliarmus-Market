<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class CouponResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'coupon' => $this->coupon_id,
            'discount' => $this->discount,
            'type' => $this->type,
            'expires_at' => $this->expires_at,
        ];
    }
}