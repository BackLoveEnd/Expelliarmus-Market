<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Http\Management\Exceptions\CannotTrashPublishedProductException;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class MoveProductToTrashAction
{
    public function handle(Product $product): void
    {
        if ($product->status->is(ProductStatusEnum::PUBLISHED)) {
            throw new CannotTrashPublishedProductException;
        }

        $product->moveToTrash();
    }
}
