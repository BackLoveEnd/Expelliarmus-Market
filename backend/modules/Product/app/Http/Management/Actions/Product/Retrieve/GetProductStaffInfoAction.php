<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Retrieve;

use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Management\Service\Product\ProductSpecificationsService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;

class GetProductStaffInfoAction
{
    public function __construct(
        private WarehouseProductInfoService $warehouseService,
        private ProductSpecificationsService $specificationsService,
    ) {}

    public function handle(int $productId): Product
    {
        $product = Product::withoutTrashed()->where('id', $productId)
            ->with([
                'category:id',
                'brand:id',
                'warehouse',
                'productSpecs' => function ($query) {
                    $query->withPivot('value');
                },
            ])
            ->firstOrFail([
                'id',
                'title',
                'category_id',
                'brand_id',
                'product_article',
                'title_description',
                'main_description_markdown',
                'images',
                'preview_image',
                'with_attribute_combinations',
                'created_at',
                'status',
                'updated_at',
            ]);

        $product->productSpecs = $this->specificationsService->prepareProductSpecifications(
            $product->productSpecs,
        );

        $product->productAttributes = $this->warehouseService->getProductAttributes(
            product: $product,
            dto: new FetchAttributesColumnsDto,
        );

        return $product;
    }
}
