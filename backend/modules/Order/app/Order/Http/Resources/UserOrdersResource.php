<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Resources;

use Illuminate\Http\Request;
use Modules\Order\Order\Models\OrderLine;
use TiMacDonald\JsonApi\JsonApiResource;

class UserOrdersResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status->toString(),
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'contact_email' => $this->contact_email,
            'positions' => $this->positions->map(function (OrderLine $position) {
                return [
                    'id' => $position->id,
                    'quantity' => $position->quantity,
                    'price_per_unit' => $position->price_per_unit_at_order_time,
                    'total_price' => $position->total_price,
                    'product' => [
                        'id' => $position->product->id,
                        'title' => $position->product->title,
                        'image' => $position->product->preview_image,
                    ],
                ];
            }),
        ];
    }
}
