<?php

namespace Modules\Product\Http\Management\Service\Attributes\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\VariationInterface;

interface ProductAttributeRetrieveInterface
{
    public function getAttributes(Product $product): Collection;

    public function loadAttributesById(Product $product, int $variationId): VariationInterface;

    public function loadAttributesByIds(\Illuminate\Support\Collection $productsWithVariations);

    public function getAttributesForProductCollection(Collection $products): Collection;
}
