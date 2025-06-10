<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Retrieve;

use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Http\Management\Service\Product\ProductSpecificationsService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;

class GetPreviewProductInformationAction
{
    public function __construct(
        private ProductImagesService $imagesService,
        private WarehouseProductInfoService $warehouseProductService,
        private ProductSpecificationsService $specificationsService,
    ) {}

    public function handle(int $productId): Product
    {
        $product = $this->loadProduct($productId);

        $product->productSpecs = $this->specificationsService->prepareProductSpecifications(
            $product->productSpecs,
        );

        if (is_null($product->images)) {
            $product->images = $this->imagesService->getProductImages($product);
        }

        if (is_null($product->hasCombinedAttributes())) {
            return $product;
        }

        $product->productAttributes = $this->warehouseProductService->getProductAttributes(
            product: $product,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [
                    ['id', 'price', 'attribute_id', 'value', 'quantity'],
                    ['id', 'name', 'type', 'view_type'],
                ],
                combinedAttrCols: [
                    ['id', 'sku', 'price', 'quantity'],
                    ['product_attributes.id', 'name', 'type', 'view_type'],
                ],
            ),
        );

        $product->previewAttributes = $this->warehouseProductService
            ->getAttributeServiceHandler()
            ->formatPreviewAttributes($product->productAttributes);

        return $product;
    }

    private function loadProduct(int $productId): Product
    {
        return Product::query()->where('id', $productId)
            ->with([
                'category:id,name,slug',
                'brand:id,name',
                'productSpecs' => function ($query) {
                    $query->withPivot('value');
                },
            ])
            ->firstOrFail();
    }
}
