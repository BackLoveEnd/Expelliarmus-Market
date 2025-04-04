<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductStatusEnum;

class GetProductBrandsByCategoryAction
{

    public function handle(Category $category): Collection
    {
        $categoryIds = $category->descendantsAndSelf($category)->pluck('id');

        return Brand::query()
            ->whereHas('products', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds)->whereStatus(ProductStatusEnum::PUBLISHED);
            })
            ->get(['id', 'name', 'slug']);
    }

}
