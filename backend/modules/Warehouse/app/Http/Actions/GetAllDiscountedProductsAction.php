<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Product\Models\Product;
use Modules\Warehouse\Filters\DiscountFinishedFilter;
use Modules\Warehouse\Filters\DiscountStatusFilter;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetAllDiscountedProductsAction
{
    public function handle(): LengthAwarePaginator
    {
        return QueryBuilder::for(Discount::class)
            ->with([
                'discountable' => function (MorphTo $morphTo) {
                    $columns = ['id', 'title', 'product_article', 'preview_image'];

                    $morphTo->morphWith([
                        ProductVariation::class => [
                            'product' => function ($query) use ($columns) {
                                $query->select(...$columns);
                            },
                        ],
                        ProductAttributeValue::class => [
                            'product' => function ($query) use ($columns) {
                                $query->select(...$columns);
                            },
                        ],
                    ]);

                    $morphTo->constrain([
                        Product::class => function ($query) use ($columns) {
                            $query->select(...$columns);
                        },
                    ]);
                },
            ])
            ->allowedFilters([
                AllowedFilter::custom('status', new DiscountStatusFilter),
                AllowedFilter::custom('finished', new DiscountFinishedFilter),
            ])
            ->allowedSorts(['start_date', 'end_date', 'percentage', 'discount_price'])
            ->paginate(config('product.retrieve.discounted'));
    }
}
