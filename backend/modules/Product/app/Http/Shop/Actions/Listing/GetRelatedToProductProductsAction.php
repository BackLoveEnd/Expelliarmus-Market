<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Listing;

use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadRelatedProductsException;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Throwable;

class GetRelatedToProductProductsAction
{
    public function __construct(
        private DiscountedProductsService $discountService,
        private WarehouseProductInfoService $warehouseService,
    ) {}

    public function handle(Category $category, int $take = 5): ?Collection
    {
        try {
            $products = Product::query()
                ->where('category_id', $category->id)
                ->whereStatus(ProductStatusEnum::PUBLISHED)
                ->inRandomOrder()
                ->take($take)
                ->get([
                    'id',
                    'slug',
                    'title',
                    'preview_image',
                    'with_attribute_combinations',
                ]);

            if ($products->isEmpty()) {
                return null;
            }

            $products = $this->warehouseService->getWarehouseInfoAboutProducts(
                products: $products,
                dto: new FetchAttributesColumnsDto(
                    singleAttrCols: [['id', 'price'], ['name']],
                    combinedAttrCols: [['id', 'price'], []],
                    warehouseCols: ['id', 'default_price', 'product_id'],
                ),
            );

            return $this->discountService->loadDiscountsForProducts($products);
        } catch (Throwable $e) {
            throw new FailedToLoadRelatedProductsException($e->getMessage());
        }
    }
}
