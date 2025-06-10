<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\VariationInterface;
use RuntimeException;

class ProductAttributeHandler
{
    private readonly ?Product $product;

    public function __construct(
        private ProductAttributeRetrieveInterface $retrieveStrategy,
    ) {}

    public function getAttributes(): Collection
    {
        if (! $this->product) {
            throw new RuntimeException('Product must be set');
        }

        return $this->retrieveStrategy->getAttributes($this->product);
    }

    public function loadAttributeById(int $variationId): VariationInterface
    {
        if (! $this->product) {
            throw new RuntimeException('Product must be set');
        }

        return $this->retrieveStrategy->loadAttributesById($this->product, $variationId);
    }

    public function loadAttributesByIds(BaseCollection $productsWithVariations): BaseCollection
    {
        return $this->retrieveStrategy->loadAttributesByIds($productsWithVariations);
    }

    public function getAttributesForCollection(Collection $products): Collection
    {
        return $this->retrieveStrategy->getAttributesForProductCollection($products);
    }

    public function formatPreviewAttributes(Collection $attributes): BaseCollection
    {
        return $this->retrieveStrategy->formatPreviewAttributes($attributes);
    }

    public function formatWarehouseAttributes(Collection $attributes): BaseCollection
    {
        return $this->retrieveStrategy->formatWarehouseInfoAttributes($attributes);
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
