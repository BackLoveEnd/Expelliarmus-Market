<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Prices;

use Illuminate\Support\Facades\DB;

class GetMinMaxProductsPriceAction
{
    public function handle(): object
    {
        return DB::table('product_min_prices')
            ->selectRaw('COALESCE(MIN(min_price), 0) as min_price, COALESCE(MAX(max_price), 0) as max_price')
            ->whereNot('min_price', 0)
            ->first();
    }
}
