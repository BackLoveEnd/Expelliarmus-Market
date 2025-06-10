<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Warehouse;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductAttributeTypeEnum;
use Modules\Warehouse\Models\ProductAttribute;
use Spatie\LaravelData\Data;

class AttributesForCombinedValueDto extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly ?int $id = null,
        public readonly ?string $attributeName = null,
        public readonly ?ProductAttributeTypeEnum $type = null,
    ) {}

    public static function collectWithCategory(mixed $items, Category $category, Collection $createdAttributes)
    {
        $createdAttributesMapped = $createdAttributes->keyBy(fn ($attr) => mb_strtolower($attr['name']));

        $newItems = collect($items)->map(function ($item) use ($createdAttributesMapped) {
            if (! array_key_exists('id', $item) || $item['id'] === null) {
                $lowerName = mb_strtolower($item['name']);
                if ($createdAttributesMapped->has($lowerName)) {
                    $attribute = $createdAttributesMapped[$lowerName];

                    return [
                        'id' => $attribute['id'],
                        'name' => $attribute['name'],
                        'type' => $item['type'],
                        'value' => $item['value'],
                    ];
                }
            }

            return $item;
        });

        self::ensureAllRequiredAttributesArePresented($category, $newItems);

        return $newItems->map(fn ($item) => new self(
            value: (string) $item['value'],
            id: $item['id'],
        ));
    }

    private static function ensureAllRequiredAttributesArePresented(Category $category, Collection $newAttributes): void
    {
        $attributes = $category
            ->allAttributesFromTree()
            ->filter(fn (ProductAttribute $attribute) => $attribute->required)
            ->pluck('id');

        $presentedRequiredAttributes = $newAttributes->whereIn('id', $attributes);

        if ($presentedRequiredAttributes->count() !== $attributes->count()) {
            throw ValidationException::withMessages([
                'combination_attributes' => 'Combination must have all required attributes. See more in category section.',
            ]);
        }
    }
}
