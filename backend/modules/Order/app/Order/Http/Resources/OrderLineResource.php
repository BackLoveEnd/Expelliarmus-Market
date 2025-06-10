<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class OrderLineResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'product' => [
                'id' => $this->product->id,
                'slug' => $this->product->slug,
                'title' => $this->product->title,
                'preview_image' => $this->product->preview_image,
            ],
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'price_per_unit' => $this->price_per_unit_at_order_time,
        ];
    }
}
