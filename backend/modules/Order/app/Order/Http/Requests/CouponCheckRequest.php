<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;

class CouponCheckRequest extends JsonApiFormRequest
{
    public function jsonApiAttributeRules(): array
    {
        return [
            'coupon' => ['required', 'string', 'size:8'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'coupon' => 'coupon code',
        ];
    }
}