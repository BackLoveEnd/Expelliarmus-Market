<?php

namespace Modules\Warehouse\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface DiscountRelationInterface
{
    public function discount(): MorphMany;
}
