<?php

namespace Modules\Brand\Http\Requests;

use App\Services\Validators\JsonApiFormRequest;
use Illuminate\Validation\Rule;
use Modules\Brand\Models\Brand;
use Modules\User\Users\Enums\RolesEnum;

class BrandRequest extends JsonApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Brand::class);
    }

    public function jsonApiAttributeRules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('brands', 'name')],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'name' => 'brand name',
            'description' => 'brand description',
        ];
    }
}
