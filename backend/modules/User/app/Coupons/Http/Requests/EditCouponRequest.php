<?php

declare(strict_types=1);

namespace Modules\User\Coupons\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Coupons\Models\Coupon;
use Modules\User\Users\Enums\RolesEnum;

class EditCouponRequest extends JsonApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Coupon::class);
    }

    public function jsonApiAttributeRules(): array
    {
        return [
            'discount' => ['required', 'numeric', 'min:1', 'max:100'],
            'expires_at' => ['required', 'date', 'after:tomorrow'],
            'email' => ['nullable', 'email', Rule::exists('users', 'email')],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'discount' => 'Discount',
            'expires_at' => 'Expiration date',
            'email' => 'Email',
        ];
    }
}
