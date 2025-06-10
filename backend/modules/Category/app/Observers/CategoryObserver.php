<?php

namespace Modules\Category\Observers;

use App\Services\Cache\CacheService;
use Modules\Category\Models\Category;
use Modules\Warehouse\Enums\ProductStatusEnum;

class CategoryObserver
{
    public function created(Category $category): void
    {
        CacheService::forgetKey(config('product.cache.root-category-children'), $category->id);
        CacheService::forgetKey(config('category.cache.category-attributes'), $category->id);
    }

    public function updated(Category $category): void
    {
        CacheService::forgetKey(config('product.cache.root-category-children'), $category->id);
        CacheService::forgetKey(config('category.cache.category-attributes'), $category->id);
    }

    public function deleted(Category $category): void
    {
        CacheService::forgetKey(config('product.cache.root-category-children'), $category->id);
        CacheService::forgetKey(config('category.cache.category-attributes'), $category->id);

        $category
            ->products()->wherePublished()
            ->update(['status' => ProductStatusEnum::NOT_PUBLISHED->value]);
    }

    public function restored(Category $category): void
    {
        //
    }

    public function forceDeleted(Category $category): void {}
}
