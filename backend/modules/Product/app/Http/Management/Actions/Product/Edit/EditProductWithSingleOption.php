<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\CreateProductDto;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\EditProductInWarehouse;

class EditProductWithSingleOption implements EditProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
        private CreateProductAttributeSingleVariationDto $singleVariationDto
    ) {
    }

    public function handle(EditProduct $editProduct, EditProductInWarehouse $editProductInWarehouse): Product
    {
        DB::transaction(function () use ($editProduct, $editProductInWarehouse) {
            $product = $editProduct->handle($this->productDto);

            $this->prepareWarehouseData();

            $warehouse = $editProductInWarehouse->handle($this->warehouseDto);
        });
    }

    private function prepareWarehouseData(): void
    {
        $this->warehouseDto->setTotalQuantity(
            $this->singleVariationDto->attributes->sum('quantity')
        );

        $this->warehouseDto->setVariationPrices(
            $this->singleVariationDto->attributes->pluck('price')
        );
    }
}