<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Category\Http\Management\DTO\EditCategoryDto;
use Modules\Category\Models\Category;

class EditCategoryAction
{
    public function handle(EditCategoryDto $categoryDto): void
    {
        DB::transaction(function () use ($categoryDto) {
            $categoryDto->category->name = $categoryDto->categoryName;

            $categoryDto->category->save();

            $this->saveAttributes($categoryDto->category, $categoryDto->attributes);
        });
    }

    private function saveAttributes(Category $category, ?array $newAttributes = null): void
    {
        if (! $newAttributes) {
            return;
        }

        $existingAttributes = collect($category->allAttributesFromTree())
            ->pluck('name')
            ->map(fn ($name) => mb_strtolower($name));

        $diffAttributes = collect($newAttributes)->filter(function (array $attribute) use (
            $existingAttributes,
        ) {
            return ! $existingAttributes->contains(mb_strtolower($attribute['name']));
        });

        if ($diffAttributes->isNotEmpty()) {
            $category->productAttributes()->createMany($diffAttributes->toArray());
        }
    }
}
