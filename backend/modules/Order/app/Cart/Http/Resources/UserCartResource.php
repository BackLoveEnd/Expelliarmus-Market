<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserCartResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_image' => $this->product_image,
            'product_title' => $this->product_title,
            'product_slug' => $this->product_slug,
            'quantity' => $this->quantity,
            'price_per_unit' => round((float) $this->price_per_unit, 2),
            'final_price' => round((float) $this->final_price, 2),
            'variation' => $this->variation,
            'discount' => $this->discount,
        ];
    }

    public function toId(Request $request): string
    {
        return $this->id;
    }

    public function toType(Request $request): string
    {
        return 'cart';
    }
}
