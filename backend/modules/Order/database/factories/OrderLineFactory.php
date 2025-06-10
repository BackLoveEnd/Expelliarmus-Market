<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Order\Models\OrderLine;
use Modules\Product\Models\Product;

class OrderLineFactory extends Factory
{
    protected $model = OrderLine::class;

    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 10),
        ];
    }

    public function product(?Product $product = null, ?int $productType = null): OrderLineFactory
    {
        return $this->state(function (array $attributes) use ($product, $productType) {
            if (! $product) {
                $product = match ($productType) {
                    Product::SINGLE_OPTION => Product::factory()->published()->withSingleAttributes(),
                    Product::COMBINED_OPTION => Product::factory()->published()->withCombinedAttributes(),
                    default => Product::factory()->published()->withoutAttributes()
                };
            }

            if (is_null($product->hasCombinedAttributes())) {
                return [
                    'variation' => null,
                    'product_id' => $product->id,
                    'price_per_unit_at_order_time' => (float) $product->warehouse->default_price,
                    'total_price' => round($attributes['quantity'] * (float) $product->warehouse->default_price, 2),
                ];
            }

            if ($product->hasCombinedAttributes()) {
                $variation = $product->combinedAttributes->first();

                $variation->loadMissing('productAttributes');

                return [
                    'variation' => [
                        'id' => $variation->id,
                        'data' => $variation->productAttributes->map(fn ($item) => [
                            'attribute_name' => $item->name,
                            'value' => $item->pivot->value,
                            'type' => $item->type->toTypes(),
                        ]),
                    ],
                    'product_id' => $product->id,
                    'price_per_unit_at_order_time' => $variation->price,
                    'total_price' => round($variation->price * $attributes['quantity'], 2),
                ];
            }

            $variation = $product->singleAttributes->first();

            $variation->loadMissing('attribute');

            return [
                'variation' => [
                    'id' => $variation->id,
                    'data' => [
                        [
                            'value' => $variation->value,
                            'attribute_type' => $variation->attribute->type->toTypes(),
                            'attribute_name' => $variation->attribute->name,
                        ],
                    ],
                ],
                'product_id' => $product->id,
                'price_per_unit_at_order_time' => $variation->price,
                'total_price' => round($variation->price * $attributes['quantity'], 2),
            ];
        });
    }
}
