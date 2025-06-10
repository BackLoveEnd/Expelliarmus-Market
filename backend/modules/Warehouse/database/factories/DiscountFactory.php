<?php

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Models\Discount;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'percentage' => fake()->numberBetween(1, 99),
            'start_date' => Carbon::now(),
            'status' => DiscountStatusEnum::ACTIVE,
            'end_date' => Carbon::now()->addMonth(),
        ];
    }

    public function product(Product $product): DiscountFactory
    {
        if (is_null($product->hasCombinedAttributes())) {
            return $this->state(function (array $attributes) use ($product) {
                return $this->applyState(
                    originalPrice: $product->warehouse->default_price,
                    percentage: $attributes['percentage'],
                    relation: $product
                );
            });
        }

        if ($product->hasCombinedAttributes()) {
            return $this->state(function (array $attributes) use ($product) {
                $variation = $product->combinedAttributes->first();

                return $this->applyState(
                    originalPrice: $variation->price,
                    percentage: $attributes['percentage'],
                    relation: $variation
                );
            });
        }

        return $this->state(function (array $attributes) use ($product) {
            $variation = $product->singleAttributes->first();

            return $this->applyState(
                originalPrice: $variation->price,
                percentage: $attributes['percentage'],
                relation: $variation
            );
        });
    }

    private function applyState(float $originalPrice, int $percentage, $relation): array
    {
        return [
            'percentage' => $percentage,
            'original_price' => $originalPrice,
            'discount_price' => $this->calculateDiscount($originalPrice, $percentage),
            'discountable_id' => $relation->id,
            'discountable_type' => $relation::class,
        ];
    }

    private function calculateDiscount(float $price, int $percentage): float
    {
        return round($price * (1 - ($percentage / 100)));
    }
}
