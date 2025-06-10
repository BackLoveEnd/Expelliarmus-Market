<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;

final class DiscountedProductsService
{
    public function getFlashSalesPaginated(int $limit, int $offset): LimitOffsetDto
    {
        $discounts = Discount::query()
            ->with([
                'discountable' => function (MorphTo $morphTo) {
                    $columns = ['id', 'title', 'preview_image', 'product_article', 'slug', 'status'];

                    $morphTo->morphWith([
                        ProductVariation::class => [
                            'product' => function ($query) use ($columns) {
                                $query->whereStatus(ProductStatusEnum::PUBLISHED)->select(...$columns);
                            },
                        ],
                        ProductAttributeValue::class => [
                            'product' => function ($query) use ($columns) {
                                $query->whereStatus(ProductStatusEnum::PUBLISHED)->select(...$columns);
                            },
                        ],
                    ]);

                    $morphTo->constrain([
                        Product::class => function ($query) use ($columns) {
                            $query->whereStatus(ProductStatusEnum::PUBLISHED)->select(...$columns);
                        },
                    ]);
                },
            ])
            ->whereStatus(DiscountStatusEnum::ACTIVE)
            ->orderBy('end_date')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $discounts = $this->filterDiscountsWithoutProduct($discounts);

        return new LimitOffsetDto(
            items: $this->uniqueDiscountsByProduct($discounts),
            total: Discount::query()->whereStatus(DiscountStatusEnum::ACTIVE)->count(),
            limit: $limit,
            offset: $offset,
        );
    }

    public function loadDiscountForProduct(Product $product): Product
    {
        if (is_null($product->hasCombinedAttributes())) {
            return $product->loadMissing('lastDiscount');
        }

        if ($product->hasCombinedAttributes()) {
            return $product->loadMissing('combinedAttributes.lastDiscount');
        }

        return $product->loadMissing('singleAttributes.lastDiscount');
    }

    public function loadLastActiveDiscountForProduct(Product $product): Product
    {
        if (is_null($product->hasCombinedAttributes())) {
            return $product->loadMissing('lastActiveDiscount');
        }

        if ($product->hasCombinedAttributes()) {
            return $product->loadMissing('combinedAttributes.lastActiveDiscount');
        }

        return $product->loadMissing('singleAttributes.lastActiveDiscount');
    }

    public function loadLastActiveDiscountForProducts(Collection $products): BaseCollection
    {
        [$withoutVariationProducts, $withVariationProducts] = $products->partition(
            fn (Product $product) => is_null($product->hasCombinedAttributes()),
        );

        $withoutVariationProducts = $withoutVariationProducts->loadMissing('lastActiveDiscount');

        [$combinedVariationProducts, $singleVariationProducts] = $withVariationProducts->partition(
            fn (Product $product) => $product->hasCombinedAttributes(),
        );

        $singleVariationProducts->loadMissing('singleAttributes.lastActiveDiscount');

        $combinedVariationProducts->loadMissing('combinedAttributes.lastActiveDiscount');

        return $withoutVariationProducts
            ->merge($singleVariationProducts)
            ->merge($combinedVariationProducts);
    }

    public function productHasActiveDiscount(Product $product): bool
    {
        $product = $this->loadLastActiveDiscountForProduct($product);

        if (is_null($product->hasCombinedAttributes())) {
            return $product->lastActiveDiscount->isNotEmpty();
        }

        if ($product->hasCombinedAttributes()) {
            return $product->combinedAttributes->lastActiveDiscount->isNotEmpty();
        }

        return $product->singleAttributes->lastActiveDiscount->isNotEmpty();
    }

    public function loadDiscountsForProducts(Collection $products, array $columns = ['*']): Collection
    {
        [$withoutVariationProducts, $withVariationProducts] = $products->partition(
            fn (Product $product) => is_null($product->hasCombinedAttributes()),
        );

        $withoutVariationProducts = $withoutVariationProducts->loadMissing([
            'discount' => fn ($query) => $query
                ->where('discounts.status', DiscountStatusEnum::ACTIVE)
                ->select($columns),
        ]);

        $withVariationProducts = $this->getLoadedDiscountedVariations($withVariationProducts, $columns);

        return $withoutVariationProducts->merge($withVariationProducts);
    }

    private function getLoadedDiscountedVariations(Collection $unloadedVariations, array $columns): Collection
    {
        [$combinedVariationProducts, $singleVariationProducts] = $unloadedVariations->partition(
            fn (Product $product) => $product->hasCombinedAttributes(),
        );

        $singleVariationProducts->loadMissing([
            'singleAttributes.discount' => fn ($query) => $query
                ->whereStatus(DiscountStatusEnum::ACTIVE)
                ->select($columns),
        ]);

        $combinedVariationProducts->loadMissing([
            'combinedAttributes.discount' => fn ($query) => $query
                ->whereStatus(DiscountStatusEnum::ACTIVE)
                ->select($columns),
        ]);

        return $singleVariationProducts->merge($combinedVariationProducts);
    }

    private function uniqueDiscountsByProduct(Collection $discountedProducts): Collection
    {
        return $discountedProducts->unique(function (Discount $discount) {
            return $discount->discountable instanceof Product
                ? $discount->discountable->id
                : $discount->discountable->product->id;
        });
    }

    private function filterDiscountsWithoutProduct(Collection $discountedProducts): Collection
    {
        return $discountedProducts->filter(function (Discount $discount) {
            return ! is_null($discount->discountable?->product);
        });
    }
}
