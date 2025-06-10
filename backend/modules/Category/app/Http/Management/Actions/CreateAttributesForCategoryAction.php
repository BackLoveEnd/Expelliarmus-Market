<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Actions;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute;

class CreateAttributesForCategoryAction
{
    public function handle(
        Category $category,
        Collection $attributes,
        string $searchKey = 'name'
    ): ProductAttribute|Collection {
        if (array_is_list($attributes->toArray())) {
            return $this->createWhenAttributesIsList($category, $attributes, $searchKey);
        }

        return $this->createWhenAttributeIsSingle($category, $attributes, $searchKey);
    }

    private function createWhenAttributesIsList(
        Category $category,
        Collection $attributes,
        string $searchKey
    ): Collection {
        $existingAttributes = $category->productAttributes()
            ->get(['id', 'name']);

        $newAttributes = $attributes->reject(fn ($attr) => in_array(
            needle: mb_strtolower($attr[$searchKey]),
            haystack: $existingAttributes->pluck('name')->map(fn ($value) => $value)
                ->toArray(),
            strict: true
        ));

        if ($newAttributes->isNotEmpty()) {
            return $category->productAttributes()->createMany($newAttributes)
                ->collect();
        }

        return $existingAttributes;
    }

    private function createWhenAttributeIsSingle(
        Category $category,
        Collection $attributes,
        string $searchKey
    ): ProductAttribute {
        $attributeValue = $attributes[$searchKey];

        $existingAttribute = $category->productAttributes()
            ->whereRaw("LOWER($searchKey) = LOWER(?)", [$attributeValue])
            ->first();

        if ($existingAttribute) {
            return $existingAttribute;
        }

        return $category->productAttributes()->create($attributes->toArray());
    }
}
