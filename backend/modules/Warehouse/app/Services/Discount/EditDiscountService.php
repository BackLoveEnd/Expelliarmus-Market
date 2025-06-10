<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\DiscountRelationInterface;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto as DiscountDto;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Http\Exceptions\VariationToApplyDiscountDoesNotExists;
use Modules\Warehouse\Models\Discount;

final class EditDiscountService extends AbstractDiscountService implements DiscountProcessingInterface
{
    public function __construct(
        Product $product,
        private Discount $discount,
    ) {
        parent::__construct($product);
    }

    /**
     * @throws DiscountIsNotRelatedToProductException
     * @throws VariationToApplyDiscountDoesNotExists
     * @throws CannotAddDiscountToProductWithoutPriceException
     */
    public function process(DiscountDto $dto): void
    {
        $this->ensureDiscountRelatedToProduct($this->discount);

        if (is_null($this->product->hasCombinedAttributes())) {
            $originalPrice = $this->product->warehouse->getRawOriginal('default_price');
        } else {
            $variation = $this->getVariationForCurrentDiscount();

            $originalPrice = $variation->getRawOriginal('price');
        }

        $this->updateDiscount(
            dto: $dto,
            originalPrice: (float) $originalPrice,
        );
    }

    private function updateDiscount(DiscountDto $dto, float $originalPrice): void
    {
        $toUpdate = [
            'percentage' => $dto->percentage,
            'end_date' => $dto->endAt,
            'discount_price' => $this->calculateDiscountPrice($originalPrice, $dto),
        ];

        if ($dto->startFrom) {
            $toUpdate['start_date'] = $dto->startFrom;
        }

        $this->discount->update($toUpdate);
    }

    private function getVariationForCurrentDiscount(): DiscountRelationInterface
    {
        $variation = $this->productVariations->filter(function ($variation) {
            return $variation->discount
                ->where('status', '!=', DiscountStatusEnum::CANCELLED->value)
                ->contains('id', $this->discount->id);
        })->first();

        if (! $variation) {
            throw new VariationToApplyDiscountDoesNotExists;
        }

        return $variation;
    }
}
