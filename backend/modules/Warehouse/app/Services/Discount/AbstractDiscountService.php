<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto as DiscountDto;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Models\Discount;

abstract class AbstractDiscountService
{
    /** @var ?Collection<int, DiscountRelationInterface> */
    protected readonly ?Collection $productVariations;

    public function __construct(protected readonly Product $product)
    {
        $this->productVariations = $this->product->getCurrentVariationRelation();
    }

    protected function calculateDiscountPrice(float $originalPrice, DiscountDto $dto): float
    {
        if ((int) $originalPrice === 0) {
            throw new CannotAddDiscountToProductWithoutPriceException;
        }

        return round(
            num: $originalPrice * (1 - ($dto->percentage / 100)),
            precision: 2,
        );
    }

    /**
     * @throws DiscountIsNotRelatedToProductException
     */
    protected function ensureDiscountRelatedToProduct(Discount $discount): void
    {
        if (is_null($this->product->hasCombinedAttributes())) {
            $this->discountRelatedToProductWithoutVariations($discount);

            return;
        }

        $this->discountRelatedToProductWithVariations($discount);
    }

    private function discountRelatedToProductWithoutVariations(Discount $discount): void
    {
        if (! $this->product->discount()->where('id', $discount->id)->exists()) {
            throw new DiscountIsNotRelatedToProductException;
        }
    }

    private function discountRelatedToProductWithVariations(Discount $discount): void
    {
        $discounts = $this->productVariations
            ->load('discount')->pluck('discount')
            ->collapse();

        if ($discounts->where('id', $discount->id)->isEmpty()) {
            throw new DiscountIsNotRelatedToProductException;
        }
    }
}
