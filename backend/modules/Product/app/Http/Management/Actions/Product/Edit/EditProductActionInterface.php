<?php

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Models\Product;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;

interface EditProductActionInterface
{
    public function handle(EditProduct $editProduct, EditProductInWarehouse $editProductInWarehouse): Product;
}
