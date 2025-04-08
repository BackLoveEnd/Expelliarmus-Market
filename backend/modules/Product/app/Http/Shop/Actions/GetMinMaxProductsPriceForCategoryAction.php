<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductStatusEnum;

class GetMinMaxProductsPriceForCategoryAction
{
    public function handle(Category $category): object
    {
        $categoryWithProducts = $category->load([
            'products' => function ($query) {
                $query->whereStatus(ProductStatusEnum::PUBLISHED)->select(['id', 'category_id']);
            },
        ]);

        return DB::table('product_min_prices')
            ->selectRaw('COALESCE(MIN(min_price), 0) as min_price, COALESCE(MAX(max_price), 0) as max_price')
            ->whereIn('product_id', $categoryWithProducts->products->pluck('id')->toArray())
            ->whereNot('min_price', 0)
            ->first();
    }
}