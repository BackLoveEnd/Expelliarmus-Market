<?php

namespace Modules\Product\Http\Management\Contracts\Storage;

use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

interface ImageManipulationInterface
{
    public function saveResized(Product $product, string $imageId, Size $size): string;

    public function getResized(Product $product, ?string $resizedImageId, Size $size): string;
}
