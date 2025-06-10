<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\Models\Product;
use Modules\Product\Rules\ProductImagesExistsRule;
use Modules\Product\Rules\ProductImagesStorageUrlRule;
use Modules\User\Users\Enums\RolesEnum;

class ProductEditImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Product::class);
    }

    public function rules(): array
    {
        return [
            'preview_image' => ['nullable', 'mimes:png,jpg,jpeg', 'max:10240'],
            'images' => ['nullable', 'array', 'max:4', new ProductImagesExistsRule($this->route('product')?->id)],
            'images.*.image' => ['nullable', 'image', 'max:10240', 'required_without:images.*.image_url'],
            'images.*.image_url' => ['nullable', 'present', 'url', new ProductImagesStorageUrlRule],
            'images.*.order' => ['required', 'integer', 'distinct', 'min:1', 'max:4'],
        ];
    }

    public function attributes(): array
    {
        return [
            'preview_image' => 'preview image',
            'images' => 'images set',
            'images.*.image' => 'image',
            'images.*.order' => 'image order',
        ];
    }
}
