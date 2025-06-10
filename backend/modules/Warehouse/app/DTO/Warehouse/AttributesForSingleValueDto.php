<?php

declare(strict_types=1);

namespace Modules\Warehouse\DTO\Warehouse;

use Spatie\LaravelData\Data;

class AttributesForSingleValueDto extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly int $quantity,
        public readonly ?float $price = null,
    ) {}
}
