<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandImageUploadRequest extends FormRequest
{
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