<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Brand\Models\Brand;
use Modules\User\Users\Enums\RolesEnum;

class BrandImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('view', Brand::class);
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048',
            ],
        ];
    }
}
