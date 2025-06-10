<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Category\Http\Management\DTO\CreateCategoryDto;
use Modules\Category\Http\Management\Exceptions\AttributesMustBeUniqueForCategoryException;
use Modules\Category\Models\Category;

class SaveCategoryWithAttributesAction
{
    public function handle(CreateCategoryDto $categoryDto): Category
    {
        return $categoryDto->wantsCreateRootCategory()
            ? $this->createRootCategory($categoryDto)
            : $this->createSubCategory($categoryDto);
    }

    private function createRootCategory(CreateCategoryDto $categoryDto): Category
    {
        return DB::transaction(function () use ($categoryDto) {
            $category = Category::query()->create(['name' => $categoryDto->categoryName]);

            $this->ensureNewAttributesUnique($category, $categoryDto->attributes);

            $category->productAttributes()->createMany($categoryDto->attributes);

            return $category;
        });
    }

    private function createSubCategory(CreateCategoryDto $categoryDto): Category
    {
        return DB::transaction(function () use ($categoryDto) {
            $parent = Category::query()->findOrFail($categoryDto->parent);

            $category = $parent->children()->create([
                'name' => $categoryDto->categoryName,
                'icon_url' => $parent->icon_url ?? null,
            ]);

            $this->ensureNewAttributesUnique($category, $categoryDto->attributes);

            $category->productAttributes()->createMany($categoryDto->attributes);

            return $category;
        });
    }

    /**
     * @throws AttributesMustBeUniqueForCategoryException
     */
    private function ensureNewAttributesUnique(Category $category, array $newAttributes): void
    {
        $existingNames = collect($category->allAttributesFromTree())
            ->pluck('name')
            ->map(fn ($name) => mb_strtolower($name));

        $newNames = collect($newAttributes)
            ->pluck('name')
            ->map(fn ($name) => mb_strtolower($name));

        if (! $existingNames->intersect($newNames)->isEmpty()) {
            throw new AttributesMustBeUniqueForCategoryException;
        }
    }
}
