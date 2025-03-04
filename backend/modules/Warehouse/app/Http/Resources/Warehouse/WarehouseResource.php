<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class WarehouseResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'price' => $this->default_price ?: 'depend on variations.',
            'quantity' => $this->total_quantity,
            'arrived_at' => $this->arrived_at.' '.config('app.timezone'),
        ];
    }
}