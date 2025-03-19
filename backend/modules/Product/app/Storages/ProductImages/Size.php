<?php

declare(strict_types=1);

namespace Modules\Product\Storages\ProductImages;

class Size
{
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {
    }
}