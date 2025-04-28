<?php

namespace Modules\Manager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Users\Enums\RolesEnum;

class ManagerRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user(RolesEnum::MANAGER->toString())?->isSuperManager();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('managers', 'email')],
        ];
    }
}
