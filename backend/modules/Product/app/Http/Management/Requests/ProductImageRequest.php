<?php

namespace Modules\Product\Http\Management\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'max:10240'],
            'preview_image' => ['nullable', 'mimes:png,jpg,jpeg', 'max:10240']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
