<?php

namespace Modules\Product\Http\Management\Requests;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Models\Product;
use Modules\Product\Rules\AllSpecificationsInGroupAreUniqueRule;
use Modules\Product\Rules\AttributeInCombinationUniqueRule;
use Modules\Product\Rules\PriceComplianceForCombinedVariationsRule;
use Modules\Product\Rules\PriceComplianceForSingleVariationsRule;
use Modules\Product\Rules\ProductSpecificationExistsRule;
use Modules\Product\Rules\ValueHasCorrectDataTypeRule;
use Modules\User\Users\Enums\RolesEnum;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;
use Modules\Warehouse\Rules\AttributesExistsInCombinedVariationsRule;
use Modules\Warehouse\Rules\UniqueSkuInCombinationsExceptSelfRule;

class ProductEditRequest extends JsonApiRelationsFormRequest
{
    private int $productId;

    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', Product::class);
    }

    public function jsonApiAttributeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'title_description' => ['required', 'string', 'max:350'],
            'main_description' => ['required', 'string'],
            'price' => [
                'nullable',
                'required_if:data.attributes.is_combined_attributes,null',
                'regex:/^\d{1,6}(\.\d{1,2})?$/',
                'max:10000000',
                'min:1',
            ],
            'total_quantity' => ['required', 'integer'],
            'product_article' => [
                'required',
                'string',
                'regex:/^\w{8}$|^\w{13}$/',
                Rule::unique('products', 'product_article')->ignore($this->productId),
            ],
            'is_combined_attributes' => ['present', 'nullable', 'boolean'],
        ];
    }

    public function jsonApiRelationshipsRules(): array
    {
        return [
            'brand' => [
                'id' => ['required', 'integer', Rule::exists('brands', 'id')],
            ],

            'category' => [
                'id' => ['required', 'integer', Rule::exists('categories', 'id')],
            ],

            'product_specs' => [
                'required',
                'array',
                new ProductSpecificationExistsRule,
            ],

            'product_specs.*' => [
                'group' => ['nullable', 'string', 'distinct:ignore_case'],
                'specifications' => ['required', 'array', new AllSpecificationsInGroupAreUniqueRule],
                'specifications.*.id' => ['nullable', 'integer'],
                'specifications.*.spec_name' => [
                    'required_without:data.relationships.product_specs.data.*.specifications.*.id',
                    'string',
                ],
                'specifications.*.value' => ['required', 'array'],
            ],

            'product_variation' => [
                'nullable',
                'present_if:data.attributes.is_combined_attributes,false',
                'exclude_if:data.attributes.is_combined_attributes,true',
                'exclude_if:data.attributes.is_combined_attributes,null',
                'array',
            ],

            'product_variation.*' => [
                'attribute_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('product_attributes', 'id'),
                ],
                'attribute_name' => [
                    'required_without:data.relationships.product_variation.data.*.attribute_id',
                    'string',
                ],
                'attribute_type' => [
                    'required_with:data.relationships.product_variation.data.*.attribute_name',
                    Rule::enum(ProductAttributeTypeEnum::class),
                ],
                'attribute_view_type' => [
                    'required',
                    Rule::enum(ProductAttributeViewTypeEnum::class),
                ],
                'attributes' => [
                    'required',
                    'array',
                    new PriceComplianceForSingleVariationsRule($this->price),
                ],
                'attributes.*.quantity' => [
                    'required',
                    'integer',
                ],
                'attributes.*.value' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $type = $this->relation('product_variation')[0]['attribute_type'];

                        (new ValueHasCorrectDataTypeRule(
                            ProductAttributeTypeEnum::tryFrom($type),
                        ))->validate($attribute, $value, $fail);
                    },
                ],
                'attributes.*.price' => [
                    'required_if:data.attributes.price,null',
                    'nullable',
                    'regex:/^\d{1,6}(\.\d{1,2})?$/',
                    'max:10000000',
                    'min:1',
                ],
            ],

            'product_variations_combinations' => [
                'nullable',
                'present_if:data.attributes.is_combined_attributes,true',
                'exclude_if:data.attributes.is_combined_attributes,false',
                'exclude_if:data.attributes.is_combined_attributes,null',
                'array',
                new AttributesExistsInCombinedVariationsRule,
                new UniqueSkuInCombinationsExceptSelfRule($this->productId),
                new PriceComplianceForCombinedVariationsRule($this->price),
            ],
            'product_variations_combinations.*' => [
                'sku' => [
                    'required',
                    'string',
                    'distinct',
                ],
                'price' => [
                    'required_if:data.attributes.price,null',
                    'nullable',
                    'regex:/^\d{1,6}(\.\d{1,2})?$/',
                    'max:10000000',
                    'min:1',
                ],
                'quantity' => [
                    'required',
                    'integer',
                ],
                'attributes' => [
                    'required',
                    'array',
                    new AttributeInCombinationUniqueRule,
                ],
                'attributes.*.id' => [
                    'nullable',
                    'integer',
                ],
                'attributes.*.value' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $type = $this->input(str_replace('.value', '.type.id', $attribute));

                        (new ValueHasCorrectDataTypeRule(
                            ProductAttributeTypeEnum::tryFrom($type),
                        ))->validate($attribute, $value, $fail);
                    },
                ],
                'attributes.*.name' => [
                    'required_without:data.relationships.product_variations_combinations.data.*.attributes.*.id',
                    'nullable',
                    'string',
                ],
                'attributes.*.type.id' => [
                    'required_with:data.relationships.product_variations_combinations.data.*.attributes.*.name',
                    'nullable',
                    Rule::enum(ProductAttributeTypeEnum::class),
                ],
                'attributes.*.attribute_view_type' => [
                    'required',
                    Rule::enum(ProductAttributeViewTypeEnum::class),
                ],
            ],
        ];
    }

    public function jsonApiRelationshipsCustomAttributes(): array
    {
        return [
            'product_variations_combinations' => 'product combinations of attributes',
            'product_variations_combinations.*' => [
                'price' => 'price',
                'sku' => 'SKU',
                'quantity' => 'quantity',
                'attributes.*.id' => 'attribute id',
                'attributes.*.value' => 'product attribute value',
                'attributes.*.price' => 'product price for attribute',
                'attributes.*.name' => 'attribute name',
                'attributes.*.type' => 'attribute type',
                'attributes.*.attribute_view_type' => 'attribute view type',
            ],
            'brand' => [
                'id' => 'brand',
            ],
            'category' => [
                'id' => 'category',
            ],
            'product_specs' => 'product specification',
            'product_specs.*' => [
                'group' => 'group',
                'specifications' => 'specifications',
                'specifications.*.id' => 'specification id',
                'specifications.*.spec_name' => 'specification name',
                'specifications.*.value' => 'specification value',
            ],
            'product_variation' => 'product variation of attributes',
            'product_variation.*' => [
                'attribute_id' => 'attribute id',
                'attribute_name' => 'attribute name',
                'attribute_type' => 'attribute type',
                'attribute_view_type' => 'attribute view type',
                'attributes' => 'product attributes',
                'attributes.*.quantity' => 'products quantity for attribute',
                'attributes.*.value' => 'product attribute value',
                'attributes.*.price' => 'product price for attribute',
            ],
        ];
    }

    public function jsonApiRelationshipsCustomErrorsMessages(): array
    {
        return [
            'product_variations_combinations' => [
                'present_if' => 'Product combination of variations must be presented in this creation type.',
                'exclude_if' => 'Product combination of variations must not be presented in this creation type.',
            ],
            'product_variations' => [
                'present_if' => 'Product single variation must be presented in this creation type.',
                'exclude_if' => 'Product single variation must not be presented in this creation type.',
            ],
            'product_variations_combinations.*' => [
                'sku.unique' => 'The SKU must be unique.',
            ],
            'product_specs.*' => [
                'group.distinct' => 'Groups in specifications must be unique.',
                'specifications.spec_name.required_without' => 'The specification name is required if you dont choose existing one.',
            ],
        ];
    }

    public function jsonApiCustomAttributes(): array
    {
        return [
            'title_description' => 'short description',
            'main_description' => 'main description',
            'total_quantity' => 'total quantity',
            'price' => 'default price',
            'title' => 'title',
            'product_article' => 'product article',
        ];
    }

    public function jsonApiCustomErrorMessages(): array
    {
        return [
            'price' => [
                'required_if' => 'The price is required',
            ],
            'product_article' => [
                'regex' => 'Product Article can only be 8 or 13 symbols.',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->productId = $this->route('product')?->id;
    }
}
