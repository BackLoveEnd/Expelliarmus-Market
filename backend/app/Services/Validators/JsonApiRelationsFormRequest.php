<?php

declare(strict_types=1);

namespace App\Services\Validators;

use Illuminate\Support\Collection;
use RuntimeException;

/**
 * @method Collection relation(string $key)
 */
abstract class JsonApiRelationsFormRequest extends JsonApiFormRequest
{
    public const string RELATION_KEY = 'data.relationships.';

    public function rules(): array
    {
        return [...$this->mapFromJsonApiAttributes(), ...$this->mapFromJsonApiRelationshipsRules()];
    }

    public function attributes(): array
    {
        return [...$this->mapToCustomAttributes(), ...$this->mapToCustomRelationshipsAttributes()];
    }

    public function messages(): array
    {
        return [...$this->mapToCustomErrorMessages(), ...$this->mapToCustomRelationshipsMessages()];
    }

    protected function mapFromJsonApiRelationshipsRules(): array
    {
        return $this->relationshipsMapperToJsonApiFields($this->jsonApiRelationshipsRules());
    }

    protected function mapToCustomRelationshipsAttributes(): array
    {
        return $this->relationshipsMapperToJsonApiFields($this->jsonApiRelationshipsCustomAttributes());
    }

    protected function mapToCustomRelationshipsMessages(): array
    {
        return $this->relationshipsMapperToJsonApiFields($this->jsonApiRelationshipsCustomErrorsMessages());
    }

    private function relationshipsMapperToJsonApiFields(array $data): array
    {
        if ($data === []) {
            return [];
        }

        return collect($data)
            ->flatMap(function ($item, $relationship) {
                if (! is_array($item)) {
                    return [self::RELATION_KEY."{$relationship}.data" => $item];
                }

                if (! str_ends_with($relationship, '.*')) {
                    if (array_is_list($item)) {
                        return [self::RELATION_KEY."{$relationship}.data" => $item];
                    }

                    return collect($item)->mapWithKeys(function ($value, $field) use ($relationship) {
                        return [self::RELATION_KEY."{$relationship}.data.{$field}" => $value];
                    });
                }

                return collect($item)
                    ->mapWithKeys(function ($value, $field) use ($relationship) {
                        $relationshipKey = str_replace('.*', '', $relationship);

                        return [self::RELATION_KEY."{$relationshipKey}.data.*.{$field}" => $value];
                    });
            })
            ->toArray();
    }

    public function __call($method, $parameters)
    {
        if ($method === 'relation') {
            if (! $parameters) {
                return collect($this->data('data.relationships'))
                    ->mapWithKeys(fn ($item, $key) => $item);
            }

            $relationKey = self::RELATION_KEY.$parameters[0];

            if (isset($this->data($relationKey)['data'])) {
                return collect($this->data($relationKey)['data']);
            }

            return collect();
        }

        throw new RuntimeException('Undefined method in json api validation');
    }

    /**
     * Applies only to the `data->relationships` field in the JSON API.
     * return [
     *       'relation' => [
     *           'key.rule' => 'message';
     *       ],
     *  ]
     */
    public function jsonApiRelationshipsCustomErrorsMessages(): array
    {
        return [];
    }

    /**
     * Applies only to the `data->relationships` field in the JSON API.
     * return [
     *      'relation' => [
     *          'field' => 'rule';
     *      ],
     * ];
     */
    abstract public function jsonApiRelationshipsRules(): array;

    /**
     * Applies only to the `data->relationships` field in the JSON API.
     * return [
     *      'relation' => [
     *          'key' => 'new attribute';
     *      ],
     * ]
     */
    abstract public function jsonApiRelationshipsCustomAttributes(): ?array;
}
