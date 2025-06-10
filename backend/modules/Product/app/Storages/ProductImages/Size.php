<?php

declare(strict_types=1);

namespace Modules\Product\Storages\ProductImages;

readonly class Size
{
    public function __construct(
        public int $width,
        public int $height
    ) {}
}
