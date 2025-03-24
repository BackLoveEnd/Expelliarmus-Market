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
        $category = Category::query()->where('slug', $value)
            ->firstOrFail(['id', '_lft', '_rgt', 'parent_id']);

        $descendants = $category->descendants->pluck('id');

        if ($descendants->isEmpty()) {
            $descendants->add($category->id);
        }

        $query->whereIn('category_id', $descendants->toArray());
    }
}