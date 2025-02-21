<?php

declare(strict_types=1);

namespace Modules\Brand\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Enums\ProductStatusEnum;

class ProductBuilder extends Builder
{
    public function wherePublished(): ProductBuilder
    {
        return $this->where('status', ProductStatusEnum::PUBLISHED->value);
    }

    public function whereNotPublished(): ProductBuilder
    {
        return $this->where('status', ProductStatusEnum::NOT_PUBLISHED->value);
    }
}