<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\Order\Rules\UniquePhoneGuestRule;
use Modules\User\Users\Enums\RolesEnum;

class GuestOrderCreateRequest extends JsonApiFormRequest
{
    public function authorize(): bool
    {
        if ($this->user(RolesEnum::MANAGER->toString())) {
            return false;
        }

        return true;
    }

    public function jsonApiAttributeRules(): array
    {
        $user = $this->user('web');

        return [
            'email' => [Rule::requiredIf(! $user), 'email', 'unique:users,email'],
            'first_name' => [Rule::requiredIf(! $user), 'string', 'max:255'],
            'last_name' => [Rule::requiredIf(! $user), 'string', 'max:255'],
            'phone' => [
                Rule::requiredIf(! $user),
                'phone:UA,US',
                'unique:users,phone_number',
                new UniquePhoneGuestRule($this->email),
            ],
            'address' => [Rule::requiredIf(! $user), 'string', 'max:255'],
            'coupon' => ['nullable', 'string'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'coupon' => 'Coupon',
        ];
    }

    public function jsonApiCustomErrorMessages(): array
    {
        return [
            'phone.phone' => 'The :attribute field must be a valid phone number.',
        ];
    }
}
