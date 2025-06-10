<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class WarehouseResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'price' => $this->default_price ?: 'depend on variations.',
            'quantity' => $this->total_quantity,
        ];

        if ($this->arrived_at) {
            $attributes['arrived_at'] = $this->arrived_at;
        }

        if ($this->status) {
            $attributes['status'] = $this->status->toString();
        }

        return $attributes;
    }
}
