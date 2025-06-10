<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions;

use Illuminate\Support\Facades\Schema;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Http\Management\Service\Product\ProductSpecificationsService;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;

class GetPublicProductInfoAction
{
    public function __construct(
        private WarehouseProductInfoService $warehouseProductService,
        private ProductImagesService $imagesService,
        private ProductSpecificationsService $specificationsService,
        private DiscountedProductsService $discountProductService,
    ) {}

    public function handle(int $productId): Product
    {
        $product = $this->loadProduct($productId);

        if (is_null($product->images)) {
            $product->images = $this->imagesService->getProductImages($product);
        }

        $product->productSpecs = $this->specificationsService->prepareProductSpecifications(
            $product->productSpecs,
        );

        $product = $this->discountProductService->loadDiscountForProduct($product);

        if (is_null($product->hasCombinedAttributes())) {
            return $product;
        }

        return $this->loadVariations($product);
    }

    private function loadProduct(int $productId): Product
    {
        $columns = array_diff(
            Schema::getColumnListing('products'),
            [ // except this columns
                'main_description_markdown',
                'preview_image',
                'preview_image_source',
                'published_at',
                'created_at',
                'updated_at',
                'document_search',
                'deleted_at',
            ],
        );

        return Product::query()
            ->where('id', $productId)
            ->wherePublished()
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'warehouse',
                'productSpecs' => function ($query) {
                    $query->withPivot('value');
                },
            ])
            ->firstOrFail($columns);
    }

    private function loadVariations(Product $product): Product
    {
        $product->productAttributes = $this->warehouseProductService->getProductAttributes(
            product: $product,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [
                    ['id', 'price', 'attribute_id', 'value', 'quantity'],
                    ['id', 'name', 'view_type'],
                ],
                combinedAttrCols: [
                    ['id', 'sku', 'price', 'quantity'],
                    ['product_attributes.id', 'name', 'view_type'],
                ],
            ),
        );

        $product->previewAttributes = $this->warehouseProductService
            ->getAttributeServiceHandler()
            ->formatPreviewAttributes($product->productAttributes);

        return $product;
    }
}
