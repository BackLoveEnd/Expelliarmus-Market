<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Prices;

use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class GetMinMaxProductsPriceForCategoryAction
{
    public function handle(Category $category): object
    {
        $categoryIds = $category->descendantsAndSelf($category)->pluck('id');

        $productIds = Product::query()->whereIn('category_id', $categoryIds)
            ->whereStatus(ProductStatusEnum::PUBLISHED)
            ->pluck('id');

        return DB::table('product_min_prices')
            ->selectRaw('COALESCE(MIN(min_price), 0) as min_price, COALESCE(MAX(max_price), 0) as max_price')
            ->whereIn('product_id', $productIds->toArray())
            ->whereNot('min_price', 0)
            ->first();
    }
}
