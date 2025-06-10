<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Support;

use Modules\Product\Models\Product;

readonly class ProductSlug
{
    public function __construct(
        private int $product,
        private ?string $slug = null,
    ) {}

    public function bind(array $columns = ['*']): Product
    {
        return Product::query()->findOrFail($this->product, $columns);
    }

    public function getProductId(): int
    {
        return $this->product;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
