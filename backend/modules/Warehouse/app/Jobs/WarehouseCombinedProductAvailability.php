<?php

namespace Modules\Warehouse\Jobs;

use App\Services\Cache\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Models\Warehouse;

class WarehouseCombinedProductAvailability implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(static function () {
            Warehouse::query()
                ->select('product_id')
                ->chunk(100, function ($products) {
                    $productsIds = $products->pluck('product_id');

                    if ($productsIds->isEmpty()) {
                        return;
                    }

                    $productsIds = $productsIds->implode(',');

                    $productUpdatedIds = DB::select(
                        '
                        UPDATE warehouses AS w
                        SET status = CASE
                            WHEN pv.has_positive = 1 AND pv.has_non_positive = 1 
                                THEN '.WarehouseProductStatusEnum::PARTIALLY->value.'
                            WHEN pv.has_positive = 1 
                                THEN '.WarehouseProductStatusEnum::IN_STOCK->value.'
                            WHEN pv.has_non_positive = 1 
                                THEN '.WarehouseProductStatusEnum::NOT_AVAILABLE->value.'
                            ELSE w.status
                        END
                        FROM (
                            SELECT product_id,
                                MAX(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) as has_positive,
                                MAX(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as has_non_positive
                            FROM product_variations
                            WHERE product_id IN ('.$productsIds.')
                            GROUP BY product_id
                        ) as pv
                        WHERE pv.product_id = w.product_id
                            AND w.product_id IN ('.$productsIds.')
                        RETURNING w.product_id
                    ',
                    );

                    $configKey = config('product.cache.product-public');

                    collect($productUpdatedIds)->each(function (object $product) use ($configKey) {
                        CacheService::forgetKey($configKey, $product->product_id);
                    });
                });
        });
    }
}
