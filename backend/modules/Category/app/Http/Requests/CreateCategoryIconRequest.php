<?php

declare(strict_types=1);

namespace Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryIconRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'icon' => ['required', 'file', 'mimes:svg']
        ];
    }
}