<?php

declare(strict_types=1);

namespace Modules\Category\Http\DTO;

use Illuminate\Support\Collection;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;

class ProductAttributesDto
{
    public function __construct(
        public readonly string $name,
        public readonly int $type,
        public readonly bool $required,
    ) {
    }

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