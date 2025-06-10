<?php

declare(strict_types=1);

namespace Modules\Order\Order\Services\CreateOrderService;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Order\Order\Dto\OrderLineDto;
use Modules\Order\Order\Dto\OrderLinesDto;
use Modules\Product\Http\Shop\Services\DiscountedProductsService;
use Modules\User\Coupons\Services\CouponManageService;
use Modules\User\Users\Models\User;
use Modules\Warehouse\Models\ProductVariation;
use Throwable;

class OrderLineService
{
    public function __construct(
        private DiscountedProductsService $discountService,
        private CouponManageService $couponService,
    ) {}

    public function prepareOrderLines(
        Collection $orderItems,
        User|string|null $user,
        ?string $couponCode,
    ): OrderLinesDto {
        $this->discountService->loadLastActiveDiscountForProducts(
            new EloquentCollection($orderItems->pluck('product')),
        );

        $orderLines = OrderLineDto::fromCheckout(
            $this->countPricesWithFormatting($orderItems),
        );

        $priceWithCoupon = $this->countTotalPriceWithCoupon(
            $orderLines,
            $user,
            $couponCode,
        );

        return OrderLinesDto::from(
            orderLines: $orderLines,
            totalPrice: $priceWithCoupon['totalPrice'],
            coupon: $priceWithCoupon['coupon'],
        );
    }

    private function countPricesWithFormatting(Collection $orderItems): Collection
    {
        return $orderItems->map(function (array $orderLineItem) {
            $variation = $orderLineItem['product']->getCurrentVariationRelation();

            $data = [
                'product' => $orderLineItem['product'],
                'quantity' => $orderLineItem['quantity'],
                'variation' => null,
                'discount' => $orderLineItem['product']->lastActiveDiscount?->first()
                    ?? $variation?->lastActiveDiscount?->first() ?? null,
            ];

            if ($variation) {
                $pricePerUnit = $variation->lastActiveDiscount->first()?->discount_price
                    ?? $variation->price;

                $data['unitPrice'] = $pricePerUnit;

                $data['totalPrice'] = $this->countPrice($pricePerUnit, $orderLineItem['quantity']);

                $data['variation'] = [
                    'id' => $orderLineItem['variation_id'],
                    'data' => $variation instanceof ProductVariation
                        ? $variation->productAttributes->map(fn ($item) => [
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
                $pricePerUnit = $orderLineItem['product']->lastActiveDiscount->first()?->discount_price
                    ?? $orderLineItem['product']->warehouse->default_price;

                $data['unitPrice'] = $pricePerUnit;

                $data['totalPrice'] = $this->countPrice($pricePerUnit, $orderLineItem['quantity']);
            }

            return (object) $data;
        });
    }

    private function countTotalPriceWithCoupon(
        Collection $orderLines,
        User|string|null $user,
        ?string $couponCode,
    ): array {
        if ($couponCode === null) {
            return [
                'coupon' => null,
                'totalPrice' => round($orderLines->sum('totalPrice'), 2),
            ];
        }

        try {
            $coupon = $this->couponService->checkCoupon($couponCode, $user);

            $totalPrice = $orderLines->sum('totalPrice');

            $totalPrice = round($totalPrice - ($totalPrice * $coupon->discount / 100), 2);

            // TODO: move removing and increase coupon to place, where order will set as paid or completed
            $this->couponService->deletePersonalCoupon($coupon);
            $this->couponService->increaseGlobalCouponUsage($coupon, $user);

            return ['coupon' => $coupon, 'totalPrice' => $totalPrice];
        } catch (Throwable $e) {
            return [
                'coupon' => null,
                'totalPrice' => round($orderLines->sum('totalPrice'), 2),
            ];
        }
    }

    private function countPrice(float $price, int $quantity): float
    {
        return round($price * $quantity, 2);
    }
}
