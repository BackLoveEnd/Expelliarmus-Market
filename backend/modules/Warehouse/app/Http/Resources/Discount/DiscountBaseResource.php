<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Discount;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountBaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'percentage' => $this->percentage,
            'old_price' => $this->original_price,
            'discount_price' => $this->discount_price,
            'start_from' => $this->start_date->format('Y-m-d H:i').' '.Carbon::now()->timezone,
            'end_at' => $this->end_date->format('Y-m-d H:i').' '.Carbon::now()->timezone,
        ];
    }
}