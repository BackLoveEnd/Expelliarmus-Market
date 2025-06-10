<?php

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Modules\Product\Models\Product;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;

interface CreateProductActionInterface
{
    public function handle(CreateProduct $createProduct, CreateProductInWarehouse $createInWarehouse): Product;
}
