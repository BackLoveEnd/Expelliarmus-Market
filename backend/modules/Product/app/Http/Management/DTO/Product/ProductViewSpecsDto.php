<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO\Product;

use Illuminate\Support\Collection;

final readonly class ProductViewSpecsDto
{
    public function __construct(
        public Collection $separatedSpecs,
        public Collection $groupedSpecs,
        public Collection $groups
    ) {}

    public function isEmpty(): bool
    {
        return $this->groups->isEmpty()
            && $this->groupedSpecs->isEmpty()
            && $this->separatedSpecs->isEmpty();
    }
}
