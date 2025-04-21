<?php

declare(strict_types=1);

namespace Modules\Order\Order\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;

class GuestOrderCreateRequest extends JsonApiFormRequest
{
    public function jsonApiAttributeRules(): array
    {
        $user = $this->user('web');

        return [
            'email' => [Rule::requiredIf(! $user), 'email'],
            'first_name' => [Rule::requiredIf(! $user), 'string', 'max:255'],
            'last_name' => [Rule::requiredIf(! $user), 'string', 'max:255'],
            'phone' => [Rule::requiredIf(! $user), 'phone:UA,US'],
            'address' => [Rule::requiredIf(! $user), 'string', 'max:255'],
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
        ];
    }
}