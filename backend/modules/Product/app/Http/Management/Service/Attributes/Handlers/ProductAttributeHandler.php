<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface;
use Modules\Product\Models\Product;

class ProductAttributeHandler
{
    public function __construct(
        private Product $product,
        private ProductAttributeRetrieveInterface $retrieveStrategy,
    ) {
    }

    public function getAttributes(): Collection
    {
        return $this->retrieveStrategy->getAttributes($this->product);
    }

    public function formatPreviewAttributes(Collection $attributes): BaseCollection
    {
        return $this->retrieveStrategy->formatPreviewAttributes($attributes);
    }

    public function formatWarehouseAttributes(Collection $attributes): BaseCollection
    {
        return $this->retrieveStrategy->formatWarehouseInfoAttributes($attributes);
    }
}