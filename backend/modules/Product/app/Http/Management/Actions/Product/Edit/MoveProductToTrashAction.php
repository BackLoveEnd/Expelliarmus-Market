<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Http\Management\Exceptions\CannotMovePublishedProductToTrashException;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;

class MoveProductToTrashAction
{
    public function handle(Product $product): void
    {
        if ($product->status === ProductStatusEnum::PUBLISHED) {
            throw new CannotMovePublishedProductToTrashException();
        }

        $product->moveToTrash();
    }
}