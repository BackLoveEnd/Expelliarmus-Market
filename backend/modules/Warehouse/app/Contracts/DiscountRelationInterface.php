<?php

namespace Modules\Warehouse\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface DiscountRelationInterface
{
    public function discount(): BelongsToMany;
}