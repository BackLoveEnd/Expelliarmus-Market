<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Category\Models\Category;
use Modules\User\Users\Enums\RolesEnum;

class CreateCategoryIconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Category::class);
    }

    public function rules(): array
    {
        return [
            'icon' => ['required', 'file', 'mimes:svg'],
        ];
    }
}
