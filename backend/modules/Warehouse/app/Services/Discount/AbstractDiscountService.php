<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto as DiscountDto;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;
use Modules\Warehouse\Http\Exceptions\VariationToApplyDiscountDoesNotExists;

abstract class AbstractDiscountService
{
    /**@var ?Collection<int, DiscountRelationInterface> $productVariations */
    protected readonly ?Collection $productVariations;

    public function __construct(protected readonly Product $product)
    {
        $this->productVariations = $this->product->getCurrentVariationRelation();
    }

    protected function calculateDiscountPrice(float $originalPrice, DiscountDto $dto): float
    {
        if ((int) $originalPrice === 0) {
            throw new CannotAddDiscountToProductWithoutPriceException();
        }

        return round(
            num: $originalPrice * (1 - ($dto->percentage / 100)),
            precision: 2,
        );
    }

    protected function getVariationForCurrentDiscount(DiscountDto $dto): DiscountRelationInterface
    {
        $variation = $this->productVariations
            ->where('id', $dto->variationId)
            ->first();

        if (! $variation) {
            throw new VariationToApplyDiscountDoesNotExists();
        }

        return $variation;
    }

    abstract public function process(DiscountDto $dto): void;
}