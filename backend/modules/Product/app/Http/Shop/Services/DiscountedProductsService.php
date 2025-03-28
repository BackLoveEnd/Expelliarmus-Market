<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\DiscountStatusEnum;
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
                    $columns = ['id', 'title', 'preview_image', 'product_article', 'slug'];

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
            ->whereStatus(DiscountStatusEnum::ACTIVE)
            ->orderBy('end_date')
            ->limit($limit)
            ->offset($offset)
            ->get();

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

    public function productHasActiveDiscount(Product $product): bool
    {
        $product = $this->loadLastActiveDiscountForProduct($product);
        if (is_null($product->hasCombinedAttributes())) {
            return $product->relationLoaded('lastActiveDiscounts')
                && $product->lastActiveDiscount->isNotEmpty();
        }

        if ($product->hasCombinedAttributes()) {
            return $product->relationLoaded('combinedAttributes.lastActiveDiscount')
                && $product->combinedAttributes->lastActiveDiscount->isNotEmpty();
        }

        return $product->relationLoaded('singleAttributes.lastActiveDiscount')
            && $product->singleAttributes->lastActiveDiscount->isNotEmpty();
    }

    public function loadDiscountsForProducts(Collection $products, array $columns = ['*']): Collection
    {
        [$withoutVariationProducts, $withVariationProducts] = $products->partition(
            fn(Product $product) => is_null($product->hasCombinedAttributes()),
        );

        $withoutVariationProducts = $withoutVariationProducts->load([
            'discount' => fn($query)
                => $query
                ->where('discounts.status', DiscountStatusEnum::ACTIVE)
                ->select($columns),
        ]);

        $withVariationProducts = $this->getLoadedDiscountedVariations($withVariationProducts, $columns);

        return $withoutVariationProducts->merge($withVariationProducts);
    }

    private function getLoadedDiscountedVariations(Collection $unloadedVariations, array $columns): Collection
    {
        [$combinedVariationProducts, $singleVariationProducts] = $unloadedVariations->partition(
            fn(Product $product) => $product->hasCombinedAttributes(),
        );

        $singleVariationProducts->loadMissing([
            'singleAttributes.discount' => fn($query)
                => $query
                ->whereStatus(DiscountStatusEnum::ACTIVE)
                ->select($columns),
        ]);

        $combinedVariationProducts->loadMissing([
            'combinedAttributes.discount' => fn($query)
                => $query
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

}
