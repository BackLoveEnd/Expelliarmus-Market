<?php

namespace Modules\Warehouse\Observers;

use App\Services\Cache\CacheService;
use Modules\Warehouse\Models\ProductAttribute;

class ProductAttributeObserver
{
    /**
     * Handle the ProductAttribute "created" event.
     */
    public function created(ProductAttribute $productAttribute): void
    {
        CacheService::forgetKey(config('category.cache.category-attributes'), $productAttribute->category_id);
    }

    /**
     * Handle the ProductAttribute "updated" event.
     */
    public function updated(ProductAttribute $productAttribute): void
    {
        CacheService::forgetKey(config('category.cache.category-attributes'), $productAttribute->category_id);
    }

    /**
     * Handle the ProductAttribute "deleted" event.
     */
    public function deleted(ProductAttribute $productAttribute): void
    {
        CacheService::forgetKey(config('category.cache.category-attributes'), $productAttribute->category_id);
    }

    /**
     * Handle the ProductAttribute "restored" event.
     */
    public function restored(ProductAttribute $productAttribute): void
    {
        //
    }

    /**
     * Handle the ProductAttribute "force deleted" event.
     */
    public function forceDeleted(ProductAttribute $productAttribute): void
    {
        //
    }
}
