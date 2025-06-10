<?php

namespace Modules\Category\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Category\Models\Category;

class CreateSubCategoryRule implements ValidationRule
{
    public function __construct(
        private string $name
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $descendantsOfParent = Category::defaultOrder()->descendantsAndSelf($value);

        $descendantsOfParent->each(function (Category $category) use ($fail) {
            if (mb_strtolower($category->name) === mb_strtolower($this->name)) {
                $fail("Category $this->name is already exists");
            }
        });
    }
}
