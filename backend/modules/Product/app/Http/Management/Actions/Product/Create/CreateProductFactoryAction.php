<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\Requests\ProductCreateRequest;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeCombinedVariationsDto;
use Modules\Warehouse\DTO\Warehouse\CreateProductAttributeSingleVariationDto;
use Modules\Warehouse\DTO\Warehouse\CreateWarehouseDto;

class CreateProductFactoryAction
{
    public function createAction(ProductCreateRequest $request): CreateProductActionInterface
    {
        if ($request->relation('product_variation')->isEmpty()
            && $request->relation('product_variations_combinations')->isEmpty()
        ) {
            return $this->createProductWithoutAttributes($request);
        }

        if ($request->relation('product_variations_combinations')->isNotEmpty()) {
            return $this->createCombinedAttributeProduct($request);
        }

        return $this->createSingleAttributeProduct($request);
    }

    private function createSingleAttributeProduct(ProductCreateRequest $request): CreateProductActionInterface
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(false);

        return new CreateProductWithSingleAttributesAction(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            singleVariationDto: CreateProductAttributeSingleVariationDto::fromRequest($request),
        );
    }

    private function createCombinedAttributeProduct(ProductCreateRequest $request): CreateProductActionInterface
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(true);

        return new CreateProductWithCombinedAttributesAction(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
            combinedVariationsDto: CreateProductAttributeCombinedVariationsDto::fromRequest($request),
        );
    }

    private function createProductWithoutAttributes(ProductCreateRequest $request): CreateProductActionInterface
    {
        $productDto = CreateProductDto::fromRequest($request);

        $productDto->setAndGetVariationType(null);

        return new CreateProductWithoutAttributes(
            productDto: $productDto,
            warehouseDto: CreateWarehouseDto::fromRequest($request),
        );
    }
}
