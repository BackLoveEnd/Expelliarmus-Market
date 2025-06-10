<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;
use Modules\Warehouse\Http\Actions\CreateProductInWarehouse;

class CreateProductWithoutAttributes implements CreateProductActionInterface
{
    public function __construct(
        private CreateProductDto $productDto,
        private CreateWarehouseDto $warehouseDto,
    ) {}

    public function handle(CreateProduct $createProduct, CreateProductInWarehouse $createInWarehouse): Product
    {
        return DB::transaction(function () use ($createProduct, $createInWarehouse) {
            $product = $createProduct->handle($this->productDto);

            $createInWarehouse->handle($product, $this->warehouseDto);

            return $product;
        });
    }
}
