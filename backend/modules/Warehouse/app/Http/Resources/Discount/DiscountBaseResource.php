<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Discount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountBaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'percentage' => $this->percentage,
            'old_price' => $this->original_price,
            'discount_price' => $this->discount_price,
            'start_from' => $this->start_date,
            'end_at' => $this->end_date,
            'status' => $this->status->toString(),
        ];
    }
}
