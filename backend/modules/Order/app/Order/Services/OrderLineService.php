<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\Warehouse\Models\ProductVariation;
use stdClass;

class OrderLineService
{
    public function __construct(
        private DiscountedProductsService $discountService,
    ) {}

    public function prepareOrderLines(Collection $orderItems): Collection
    {
        $this->discountService->loadLastActiveDiscountForProducts(
            new EloquentCollection($orderItems->pluck('product')),
        );

        return OrderLineDto::fromCheckout(
            items: $this->countPricesWithFormatting($orderItems),
        );
    }

    private function countPricesWithFormatting(Collection $orderItems)
    {
        return $orderItems->map(function (stdClass $orderLineItem) {
            $variation = $orderLineItem->product->getCurrentVariationRelation();

            $data = [
                'product' => $orderLineItem->product,
                'quantity' => $orderLineItem->quantity,
                'variation' => null,
            ];

            if ($variation) {
                $pricePerUnit = $variation->lastActiveDiscount->first()?->discount_price
                    ?? $variation->price;

                $data['unitPrice'] = $pricePerUnit;

                $data['totalPrice'] = $this->countPrice($pricePerUnit, $orderLineItem->quantity);

                $data['variation'] = [
                    'id' => $orderLineItem->variation_id,
                    'data' => $variation instanceof ProductVariation
                        ? $variation->productAttributes->map(fn($item)
                            => [
                            'attribute_name' => $item->name,
                            'value' => $item->pivot->value,
                            'type' => $item->type->toTypes(),
                        ])
                        : [
                            [
                                'value' => $variation->value,
                                'attribute_type' => $variation->attribute->type->toTypes(),
                                'attribute_name' => $variation->attribute->name,
                            ],
                        ],
                ];
            } else {
                $pricePerUnit = $orderLineItem->product->lastActiveDiscount->first()?->discount_price
                    ?? $orderLineItem->product->warehouse->default_price;

                $data['unitPrice'] = $pricePerUnit;

                $data['totalPrice'] = $this->countPrice($pricePerUnit, $orderLineItem->quantity);
            }

            return (object)$data;
        });
    }

    private function countPrice(float $price, int $quantity): float
    {
        return round($price * $quantity, 2);
    }
}