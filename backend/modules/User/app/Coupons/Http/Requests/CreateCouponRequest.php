<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Coupons\Enum\CouponTypeEnum;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Enums\RolesEnum;

class CreateCouponRequest extends JsonApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Coupon::class);
    }

    public function jsonApiAttributeRules(): array
    {
        return [
            'coupon_code' => ['nullable', 'string', Rule::unique('coupons', 'coupon_id')],
            'discount' => ['required', 'integer', 'between:1,100'],
            'type' => [
                'required',
                'string',
                Rule::in([CouponTypeEnum::GLOBAL->toString(), CouponTypeEnum::PERSONAL->toString()]),
            ],
            'email' => [
                'nullable',
                'required_if:data.attributes.type,'.CouponTypeEnum::PERSONAL->toString(),
                'email',
                Rule::exists('users', 'email'),
            ],
            'expires_at' => ['required', 'date', 'after:tomorrow'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'coupon_code' => 'Coupon Code',
            'discount' => 'Discount',
            'type' => 'Type',
            'email' => 'Email',
            'expires_at' => 'Expires At',
        ];
    }
}
