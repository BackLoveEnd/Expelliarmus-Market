<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Category\Models\Category;
use Spatie\QueryBuilder\Filters\Filter;

class CategoryFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $categoriesQuery = Category::query()->with('descendants');

        if (is_array($value)) {
            $ids = array_filter($value, fn ($v) => ctype_digit((string) $v));
            $slugs = array_filter($value, fn ($v) => ! ctype_digit((string) $v));

            $categoriesQuery->whereIn('id', $ids)->orWhereIn('slug', $slugs);
        } else {
            $categoriesQuery->when(
                ctype_digit((string) $value),
                fn ($q) => $q->where('id', $value),
                fn ($q) => $q->where('slug', $value),
            );
        }

        $categories = $categoriesQuery->get(['id', '_lft', '_rgt', 'parent_id']);

        $descendants = $categories->flatMap(
            fn ($category) => $category->descendants->pluck('id')->prepend($category->id),
        );

        $query->whereIn('category_id', $descendants->unique()->toArray());
    }
}
