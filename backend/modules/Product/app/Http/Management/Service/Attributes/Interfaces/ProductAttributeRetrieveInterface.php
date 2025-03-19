<?php

namespace Modules\Product\Http\Management\Service\Attributes\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

interface ProductAttributeRetrieveInterface
{
    public function getAttributes(Product $product): Collection;
}