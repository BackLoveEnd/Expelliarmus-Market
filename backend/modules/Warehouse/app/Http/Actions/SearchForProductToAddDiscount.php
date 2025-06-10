<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class SearchForProductToAddDiscount
{
    public function handle(mixed $searchable): Collection
    {
        return Product::withTrashed()
            ->whereStatus([ProductStatusEnum::PUBLISHED, ProductStatusEnum::NOT_PUBLISHED])
            ->search($searchable)
            ->get(['id', 'title', 'product_article']);
    }
}
