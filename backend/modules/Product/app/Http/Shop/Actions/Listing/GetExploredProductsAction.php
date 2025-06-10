<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Listing;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadExploreProductsException;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Throwable;

class GetExploredProductsAction
{
    public function __construct(private WarehouseProductInfoService $warehouseService) {}

    public function handle(int $take): ?Collection
    {
        try {
            $products = Product::query()
                ->withoutDiscounts()
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

            return $this->mapPricesForProducts(
                products: $this->getWarehouseInfo($products),
            );
        } catch (Throwable $e) {
            throw new FailedToLoadExploreProductsException($e->getMessage());
        }
    }

    private function getWarehouseInfo(Collection $products): Collection
    {
        return $this->warehouseService->getWarehouseInfoAboutProducts(
            products: $products,
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price'], ['name']],
                combinedAttrCols: [['id', 'price'], []],
                warehouseCols: ['id', 'default_price', 'product_id'],
            ),
        );
    }

    private function mapPricesForProducts(Collection $products): Collection
    {
        return $products->each(function (Product $product) {
            if (is_null($product->hasCombinedAttributes())) {
                $product->price = $product->warehouse->default_price;
            } elseif ($product->hasCombinedAttributes()) {
                $product->price = $product->combinedAttributes->first()->price;
            } else {
                $product->price = $product->singleAttributes->first()->price;
            }
        });
    }
}
