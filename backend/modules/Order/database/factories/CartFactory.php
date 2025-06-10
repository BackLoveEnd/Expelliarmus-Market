<?php

declare(strict_types=1);

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Contracts\VariationInterface;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 5),
            'discount' => null,
            'variation' => null,
        ];
    }

    public function product(Product $product, ?VariationInterface $variation = null): CartFactory
    {
        return $this->state(function (array $attributes) use ($product, $variation) {
            if (is_null($product->hasCombinedAttributes())) {
                return [
                    'product_id' => $product->id,
                    'variation' => null,
                    'price_per_unit' => $product->warehouse->default_price,
                    'final_price' => round($product->warehouse->default_price * $attributes['quantity'], 2),
                ];
            }

            if ($product->hasCombinedAttributes()) {
                $variation->loadMissing('productAttributes');

                return [
                    'product_id' => $product->id,
                    'price_per_unit' => $variation->price,
                    'final_price' => round($variation->price * $attributes['quantity'], 2),
                    'variation' => [
                        'id' => $variation->id,
                        'data' => $variation->productAttributes->map(fn ($item) => [
                            'attribute_name' => $item->name,
                            'value' => $item->pivot->value,
                            'type' => $item->type->toTypes(),
                        ]),
                    ],
                ];
            }

            $variation->loadMissing('attribute');

            return [
                'product_id' => $product->id,
                'price_per_unit' => $variation->price,
                'final_price' => round($variation->price * $attributes['quantity'], 2),
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
            ];
        });
    }

    public function forUser(User $user): CartFactory
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
