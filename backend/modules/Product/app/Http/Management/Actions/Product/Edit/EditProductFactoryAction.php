<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Http\Management\DTO\CreateProductDto;
use Modules\Product\Http\Management\Requests\ProductEditRequest;
use Modules\Warehouse\DTO\CreateProductAttributeCombinedVariationsDto;
use Modules\Warehouse\DTO\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\CreateWarehouseDto;

class EditProductFactoryAction
{
    public function createAction(ProductEditRequest $request): EditProductActionInterface
    {
        if (is_null($request->is_combined_attributes)) {
            return $this->editProductWithoutOptions($request);
        }

        if ($request->is_combined_attributes === true) {
            return $this->editCombinedOptionsProduct($request);
        }

        return $this->editSingleOptionProduct($request);
    }

    private function editSingleOptionProduct(ProductEditRequest $request): EditProductWithSingleOption
    {
        return new EditProductWithSingleOption(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            singleVariationDto: CreateProductAttributeSingleVariationDto::fromRequest($request)
        );
    }

    private function editCombinedOptionsProduct(ProductEditRequest $request): EditProductWithCombinedOptions
    {
        return new EditProductWithCombinedOptions(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            combinedVariationsDto: CreateProductAttributeCombinedVariationsDto::fromRequest($request)
        );
    }

    private function editProductWithoutOptions(ProductEditRequest $request): EditProductWithoutOptions
    {
        return new EditProductWithoutOptions(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request)
        );
    }
}