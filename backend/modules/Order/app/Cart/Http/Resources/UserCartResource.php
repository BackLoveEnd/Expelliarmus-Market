<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserCartResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        dd($this->resource);
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price_per_unit' => $this->price_per_unit,
            'final_price' => $this->final_price,
            'variation' => $this->variation,
            'discount' => $this->discount,
        ];
    }

    public function toId(Request $request): string
    {
        return (string)$this->product_id;
    }

    public function toType(Request $request): string
    {
        return 'cart';
    }
}