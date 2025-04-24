<?php

declare(strict_types=1);

namespace Modules\Order\Order\Dto;

use Illuminate\Support\Collection;

final readonly class OrderLinesDto
{
    public function __construct(
        /**@var Collection<OrderLineDto> $orderLines */
        public Collection $orderLines,
        public float $totalPrice,
    ) {}

    public static function from(Collection $orderLines, float $totalPrice): self
    {
        return new self(
            orderLines: $orderLines,
            totalPrice: $totalPrice,
        );
    }
}