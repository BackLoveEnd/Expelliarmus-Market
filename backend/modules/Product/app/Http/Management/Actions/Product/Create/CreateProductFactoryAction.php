<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Modules\Product\Http\Management\DTO\CreateProductDto;
use Modules\Product\Http\Management\Requests\ProductCreateRequest;
use Modules\Warehouse\DTO\CreateProductAttributeCombinedVariationsDto;
use Modules\Warehouse\DTO\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\CreateWarehouseDto;

class CreateProductFactoryAction
{
    public function createAction(ProductCreateRequest $request): CreateProductActionInterface
    {
        if (is_null($request->is_combined_attributes)) {
            return $this->createProductWithoutAttributes($request);
        }

        if ($request->is_combined_attributes === true) {
            return $this->createCombinedAttributeProduct($request);
        }

        return $this->createSingleAttributeProduct($request);
    }

    private function createSingleAttributeProduct(ProductCreateRequest $request): CreateProductActionInterface
    {
        return new CreateProductWithSingleAttributesAction(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            singleVariationDto: CreateProductAttributeSingleVariationDto::fromRequest($request)
        );
    }

    private function createCombinedAttributeProduct(ProductCreateRequest $request): CreateProductActionInterface
    {
        return new CreateProductWithCombinedAttributesAction(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            combinedVariationsDto: CreateProductAttributeCombinedVariationsDto::fromRequest($request)
        );
    }

    private function createProductWithoutAttributes(ProductCreateRequest $request): CreateProductActionInterface
    {
        return new CreateProductWithoutAttributes(
            productDto: CreateProductDto::fromRequest($request),
            warehouseDto: CreateWarehouseDto::fromRequest($request)
        );
    }
}