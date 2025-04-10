<?php

namespace Modules\Manager\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;

class ManagerRegisterRequest extends JsonApiFormRequest
{
    public function jsonApiAttributeRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('managers', 'email')],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email',
        ];
    }
}
