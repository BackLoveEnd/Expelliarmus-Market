<?php

declare(strict_types=1);

namespace Modules\Order\Order\Dto;

use Illuminate\Support\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\Discount;
use stdClass;

final readonly class OrderLineDto
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public float $totalPrice,
        public float $unitPriceAtOrderTime,
        public ?int $orderId = null,
        public ?array $variation = null,
        public ?Discount $discount = null,
    ) {}

    public static function fromCheckout(Collection $items): Collection
    {
        return $items->map(function (stdClass $element) {
            return new self(
                product: $element->product,
                quantity: $element->quantity,
                totalPrice: $element->totalPrice,
                unitPriceAtOrderTime: $element->unitPrice,
                orderId: null,
                variation: $element->variation ?? null,
                discount: $element->discount ?? null,
            );
        });
    }
}
