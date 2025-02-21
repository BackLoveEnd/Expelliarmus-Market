<?php

declare(strict_types=1);

namespace Modules\Category\Http\DTO;

use Modules\Category\Http\Requests\CreateCategoryRequest;

readonly class CreateCategoryDto
{
    public function __construct(
        public string $categoryName,
        public ?int $parent = null,
        public ?array $attributes = null
    ) {
    }

    public static function fromRequest(CreateCategoryRequest $request): CreateCategoryDto
    {
        return new self(
            categoryName: $request->name,
            parent: $request->parent ?? null,
            attributes: $request->relation('attributes')->toArray()
        );
    }

    public function wantsCreateRootCategory(): bool
    {
        return is_null($this->parent);
    }
}