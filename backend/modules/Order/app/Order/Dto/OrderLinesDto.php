<?php

declare(strict_types=1);

namespace Modules\Order\Order\Dto;

use Illuminate\Support\Collection;
use Modules\User\Coupons\Models\Coupon;

final readonly class OrderLinesDto
{
    public function __construct(
        /** @var Collection<OrderLineDto> $orderLines */
        public Collection $orderLines,
        public float $totalPrice,
        public ?Coupon $coupon = null,
    ) {}

    public static function from(Collection $orderLines, float $totalPrice, ?Coupon $coupon): self
    {
        return new self(
            orderLines: $orderLines,
            totalPrice: $totalPrice,
            coupon: $coupon,
        );
    }
}
