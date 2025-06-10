<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Requests;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Validation\Rule;
use Modules\Category\Models\Category;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class EditCategoryRequest extends JsonApiRelationsFormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Category::class);
    }

    public function jsonApiAttributeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    public function jsonApiRelationshipsRules(): array
    {
        return [
            'attributes' => ['nullable', 'array'],
            'attributes.*' => [
                'name' => ['required', 'string', 'max:50'],
                'type' => ['required', Rule::enum(ProductAttributeTypeEnum::class)],
                'required' => ['nullable', 'boolean'],
            ],
        ];
    }

    public function jsonApiRelationshipsCustomAttributes(): ?array
    {
        return [
            'attributes' => 'attributes',
            'attributes.*' => [
                'name' => 'attribute name',
                'type' => 'attribute type',
                'required' => 'required',
            ],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'name' => 'name',
            'parent' => 'parent',
        ];
    }
}
