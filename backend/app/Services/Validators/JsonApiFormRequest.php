<?php

declare(strict_types=1);

namespace App\Services\Validators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

abstract class JsonApiFormRequest extends FormRequest
{
    public const string ATTRIBUTE_KEY = 'data.attributes.';

    public function rules(): array
    {
        return [...$this->mapFromJsonApiAttributes()];
    }

    public function attributes(): array
    {
        return [...$this->mapToCustomAttributes()];
    }

    public function messages(): array
    {
        return [...$this->mapToCustomErrorMessages()];
    }

    protected function mapFromJsonApiAttributes(): array
    {
        return Arr::prependKeysWith($this->jsonApiAttributeRules(), self::ATTRIBUTE_KEY);
    }

    protected function mapToCustomAttributes(): array
    {
        return Arr::prependKeysWith($this->jsonApiCustomAttributes(), self::ATTRIBUTE_KEY);
    }

    protected function mapToCustomErrorMessages(): array
    {
        if (! $this->jsonApiCustomErrorMessages()) {
            return [];
        }

        return Arr::prependKeysWith($this->jsonApiCustomErrorMessages(), self::ATTRIBUTE_KEY);
    }

    public function __get($key)
    {
        return $this->data(self::ATTRIBUTE_KEY.$key);
    }

    /**
     * Applies only to the `data->attributes` field in the JSON API.
     * Use `'field' => 'rule'` to add validation rule,
     * where `field` represents the property name only.
     */
    abstract public function jsonApiAttributeRules(): array;

    /**
     * Applies only to the `data->attributes` field in the JSON API.
     * Use `'field' => 'new attribute'` to rename an attribute,
     * where `field` represents the property name only.
     */
    abstract public function jsonApiCustomAttributes(): array;

    /**
     * Applies only to the `data->attributes` field in the JSON API.
     * Use `'field.rule' => 'message'` to change error message for rule,
     * where `field` represents the property name only.
     */
    public function jsonApiCustomErrorMessages(): array
    {
        return [];
    }
}
