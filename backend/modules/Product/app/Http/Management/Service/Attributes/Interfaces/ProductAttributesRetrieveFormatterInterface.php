<?php

namespace Modules\Product\Http\Management\Service\Attributes\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface ProductAttributesRetrieveFormatterInterface
{
    public function formatPreviewAttributes(Collection $attributes): BaseCollection;

    public function formatWarehouseInfoAttributes(Collection $attributes): BaseCollection;
}
