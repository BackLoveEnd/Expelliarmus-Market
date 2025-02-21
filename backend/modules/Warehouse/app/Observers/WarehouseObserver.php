<?php

namespace Modules\Warehouse\Observers;

use App\Services\Cache\CacheService;
use Modules\Warehouse\Models\Warehouse;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        CacheService::forgetKey(config('warehouse.cache.product-warehouse-info'), $warehouse->product_id);
    }

    /**
     * Handle the Warehouse "updated" event.
     */
    public function updated(Warehouse $warehouse): void
    {
        CacheService::forgetKey(config('warehouse.cache.product-warehouse-info'), $warehouse->product_id);
    }

    /**
     * Handle the Warehouse "deleted" event.
     */
    public function deleted(Warehouse $warehouse): void
    {
        CacheService::forgetKey(config('warehouse.cache.product-warehouse-info'), $warehouse->product_id);
    }

    /**
     * Handle the Warehouse "restored" event.
     */
    public function restored(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "force deleted" event.
     */
    public function forceDeleted(Warehouse $warehouse): void
    {
        CacheService::forgetKey(config('warehouse.cache.product-warehouse-info'), $warehouse->product_id);
    }
}
