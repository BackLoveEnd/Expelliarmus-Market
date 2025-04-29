<?php

namespace Modules\Product\Http\Management\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\Models\Product;
use Modules\User\Users\Enums\RolesEnum;

class ProductImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => ['nullable', 'array', 'max:4'],
            'images.*.image' => ['required', 'image', 'max:10240'],
            'images.*.order' => ['required', 'integer', 'distinct', 'min:1', 'max:4'],
            'preview_image' => ['nullable', 'mimes:png,jpg,jpeg', 'max:10240'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Product::class);
    }
}
