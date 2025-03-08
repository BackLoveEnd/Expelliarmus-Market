<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Requests\ProductEditRequest;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeCombinedVariationsDto;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;

class EditProductFactoryAction
{
    public function createAction(ProductEditRequest $request): EditProductActionInterface
    {
        if ($request->relation('product_variation')->isEmpty()
            && $request->relation('product_variations_combinations')->isEmpty()
        ) {
            return $this->editProductWithoutOptions($request);
        }

        if ($request->relation('product_variations_combinations')->isNotEmpty()) {
            return $this->editCombinedOptionsProduct($request);
        }

        return $this->editSingleOptionProduct($request);
    }

    private function editSingleOptionProduct(ProductEditRequest $request): EditProductWithSingleOption
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(false);

        return new EditProductWithSingleOption(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            singleVariationDto: CreateProductAttributeSingleVariationDto::fromRequest($request),
        );
    }

    private function editCombinedOptionsProduct(ProductEditRequest $request): EditProductWithCombinedOptions
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(true);

        return new EditProductWithCombinedOptions(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            combinedVariationsDto: CreateProductAttributeCombinedVariationsDto::fromRequest($request),
        );
    }

    private function editProductWithoutOptions(ProductEditRequest $request): EditProductWithoutOptions
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(null);

        return new EditProductWithoutOptions(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
        );
    }
}
