<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Brand\Models\Brand;
use Modules\Warehouse\Enums\ProductStatusEnum;

class GetCategoriesForBrandAction
{
    public function handle(string|int $brandId): Collection
    {
        $brand = Brand::query()
            ->when(
                value: is_numeric($brandId),
                callback: fn ($query) => $query->where('id', $brandId),
                default: fn ($query) => $query->where('slug', $brandId),
            )
            ->firstOrFail();

        return $brand
            ->categories()
            ->where('products.status', ProductStatusEnum::PUBLISHED)
            ->orderBy('categories.name')
            ->get(['categories.id', 'categories.name', 'categories.slug']);
    }
}
