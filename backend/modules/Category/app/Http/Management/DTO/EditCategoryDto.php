<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\DTO;

use Modules\Category\Http\Management\Requests\EditCategoryRequest;
use Modules\Category\Models\Category;
use Spatie\LaravelData\Data;

class EditCategoryDto extends Data
{
    public function __construct(
        public Category $category,
        public string $categoryName,
        public ?array $attributes = null
    ) {}

    public static function fromRequest(EditCategoryRequest $request): EditCategoryDto
    {
        return new self(
            category: Category::query()->findOrFail((int) $request->route('category')),
            categoryName: $request->name,
            attributes: $request->relation('attributes')->toArray()
        );
    }
}
