<?php

namespace Modules\Manager\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;

class ManagerLoginRequest extends JsonApiFormRequest
{

    public function jsonApiAttributeRules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('managers', 'email')],
            'password' => ['required', 'string'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'password',
        ];
    }
}
