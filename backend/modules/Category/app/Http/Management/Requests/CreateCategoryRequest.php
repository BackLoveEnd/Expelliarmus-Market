<?php

namespace Modules\Category\Http\Management\Requests;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Validation\Rule;
use Modules\Category\Models\Category;
use Modules\Category\Rules\CreateSubCategoryRule;
use Modules\Category\Rules\UniqueRootCategoryRule;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class CreateCategoryRequest extends JsonApiRelationsFormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Category::class);
    }

    public function jsonApiAttributeRules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'parent' => ['nullable', 'integer'],
        ];

        if ($this->input('data.attributes.parent') !== null) {
            $rules['parent'][] = new CreateSubCategoryRule($this->input('data.attributes.name'));
        } else {
            $rules['name'][] = new UniqueRootCategoryRule;
        }

        return $rules;
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
