<?php

namespace Modules\Warehouse\Jobs;

use App\Services\Cache\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;

class WarehouseDefaultProductAvailability implements ShouldQueue
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
            Product::query()
                ->whereWithoutVariations()
                ->select('id')
                ->chunk(100, function ($products) {
                    $productsIds = $products->pluck('id');

                    if ($productsIds->isEmpty()) {
                        return;
                    }

                    $productsIds = $productsIds->implode(',');

                    $updatedProducts = DB::select(
                        '
                            UPDATE warehouses w
                            SET status = CASE 
                                WHEN w.total_quantity > 0 
                                    THEN '.WarehouseProductStatusEnum::IN_STOCK->value.'
                                WHEN w.total_quantity <= 0 
                                    THEN '.WarehouseProductStatusEnum::NOT_AVAILABLE->value."
                                ELSE w.status
                            END
                            WHERE w.product_id IN ($productsIds)
                            RETURNING w.product_id
                        ",
                    );

                    $configKey = config('product.cache.product-public');

                    collect($updatedProducts)->each(function (object $product) use ($configKey) {
                        CacheService::forgetKey($configKey, $product->product_id);
                    });
                });
        });
    }
}
