<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Prices;

use Illuminate\Support\Facades\DB;
use Modules\Brand\Models\Brand;
use Modules\Warehouse\Enums\ProductStatusEnum;

class GetMinMaxProductPriceForBrandAction
{
    public function handle(Brand $brand)
    {
        $productsIds = $brand->products()->whereStatus(ProductStatusEnum::PUBLISHED)->pluck('id');

        return DB::table('product_min_prices')
            ->selectRaw('COALESCE(MIN(min_price), 0) as min_price, COALESCE(MAX(max_price), 0) as max_price')
            ->whereIn('product_id', $productsIds->toArray())
            ->whereNot('min_price', 0)
            ->first();
    }
}
