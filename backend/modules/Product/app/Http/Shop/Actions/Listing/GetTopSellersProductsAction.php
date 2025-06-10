<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Actions\Listing;

use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Order\Models\OrderLine;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Http\Shop\Exceptions\FailedToLoadTopSellersException;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Throwable;

class GetTopSellersProductsAction
{
    public function __construct(
        private DiscountedProductsService $discountService,
        private WarehouseProductInfoService $warehouseService,
    ) {}

    public function handle(int $limit = 5): ?Collection
    {
        try {
            $orderLines = OrderLine::query()
                ->with([
                    'product' => function ($query) {
                        $query->select([
                            'id',
                            'slug',
                            'title',
                            'preview_image',
                            'with_attribute_combinations',
                        ]);
                    },
                ])
                ->selectRaw('product_id, COUNT(*) as total_count')
                ->orderBy('total_count', 'desc')
                ->groupBy('product_id')
                ->take($limit)
                ->get();

            $products = $orderLines->pluck('product');

            if ($products->isEmpty()) {
                return null;
            }

            $products = $this->warehouseService->getWarehouseInfoAboutProducts(
                products: new Collection($products),
                dto: new FetchAttributesColumnsDto(
                    singleAttrCols: [['id', 'price'], ['name']],
                    combinedAttrCols: [['id', 'price'], []],
                    warehouseCols: ['id', 'default_price', 'product_id'],
                ),
            );

            return $this->discountService->loadDiscountsForProducts($products);
        } catch (Throwable $e) {
            throw new FailedToLoadTopSellersException($e->getMessage());
        }
    }
}
