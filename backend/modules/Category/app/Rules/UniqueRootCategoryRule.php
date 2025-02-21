<?php

namespace Modules\Category\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Category\Models\Category;

class UniqueRootCategoryRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Category::query()->whereNull('parent_id')->where('name', $value)->exists()) {
            $fail("Root category with name $value is exists");
        }
    }
}
