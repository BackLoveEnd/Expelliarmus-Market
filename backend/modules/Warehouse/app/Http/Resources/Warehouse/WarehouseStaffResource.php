<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class WarehouseStaffResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'total_quantity' => $this->total_quantity,
            'default_price' => $this->default_price,
        ];
    }
}
