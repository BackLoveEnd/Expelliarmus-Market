<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Discount;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\DTO\Discount\ProductDiscountDto as DiscountDto;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;
use Modules\Warehouse\Http\Exceptions\DiscountIsNotRelatedToProductException;
use Modules\Warehouse\Http\Exceptions\VariationToApplyDiscountDoesNotExists;
use Modules\Warehouse\Models\Discount;

class EditDiscountService extends AbstractDiscountService
{
    public function __construct(
        protected readonly Product $product,
        private Discount $discount,
    ) {
        parent::__construct($this->product);
    }

    /**
     * @param  DiscountDto  $dto
     * @return void
     * @throws DiscountIsNotRelatedToProductException
     * @throws VariationToApplyDiscountDoesNotExists
     * @throws CannotAddDiscountToProductWithoutPriceException
     */
    public function process(DiscountDto $dto): void
    {
        $this->ensureDiscountRelatedToProduct();

        if (is_null($this->product->hasCombinedAttributes())) {
            $originalPrice = $this->product->warehouse->getRawOriginal('default_price');
        } else {
            $variation = $this->getVariationForCurrentDiscount($dto);

            $originalPrice = $variation->getRawOriginal('price');
        }

        $this->updateDiscount(
            dto: $dto,
            originalPrice: $originalPrice,
        );
    }

    /**
     * @param  DiscountDto  $dto
     * @param  float  $originalPrice
     * @return void
     * @throws CannotAddDiscountToProductWithoutPriceException
     */
    protected function updateDiscount(DiscountDto $dto, float $originalPrice): void
    {
        $this->discount->update([
            'percentage' => $dto->percentage,
            'start_date' => $dto->startFrom,
            'end_date' => $dto->endAt,
            'discount_price' => $this->calculateDiscountPrice($originalPrice, $dto),
        ]);
    }

    /**
     * @return void
     * @throws DiscountIsNotRelatedToProductException
     */
    protected function ensureDiscountRelatedToProduct(): void
    {
        if (is_null($this->product->hasCombinedAttributes())) {
            $this->discountRelatedToProductWithoutVariations();

            return;
        }

        $this->discountRelatedToProductWithVariations($this->productVariations);
    }

    /**
     * @return void
     * @throws DiscountIsNotRelatedToProductException
     */
    private function discountRelatedToProductWithoutVariations(): void
    {
        if (! $this->product->discount()->where('discount_id', $this->discount->id)->exists()) {
            throw new DiscountIsNotRelatedToProductException();
        }
    }

    /**
     * @param  Collection  $variations
     * @return void
     * @throws DiscountIsNotRelatedToProductException
     */
    private function discountRelatedToProductWithVariations(Collection $variations): void
    {
        $discounts = $variations
            ->load('discount')->pluck('discount')
            ->collapse();

        if ($discounts->where('id', $this->discount->id)->isEmpty()) {
            throw new DiscountIsNotRelatedToProductException();
        }
    }
}