<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;

class EditProductWithoutOptions implements EditProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
    ) {}

    public function handle(EditProduct $editProduct, EditProductInWarehouse $editProductInWarehouse): Product
    {
        return DB::transaction(function () use ($editProduct, $editProductInWarehouse) {
            $product = $editProduct->handle($this->productDto);

            $editProductInWarehouse->handle($this->warehouseDto);

            return $product;
        });
    }
}
