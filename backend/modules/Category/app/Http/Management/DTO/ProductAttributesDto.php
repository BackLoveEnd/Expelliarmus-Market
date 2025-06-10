<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\DTO;

use Illuminate\Support\Collection;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

readonly class ProductAttributesDto
{
    public function __construct(
        public string $name,
        public int $type,
        public bool $required,
    ) {}

    public static function collect(Collection $attributes): Collection
    {
        return $attributes->map(function (array $attributes) {
            return new self(
                name: $attributes['name'],
                type: ProductAttributeTypeEnum::tryFrom($attributes['type'])->value,
                required: $attributes['required'] ?? false
            );
        });
    }
}
