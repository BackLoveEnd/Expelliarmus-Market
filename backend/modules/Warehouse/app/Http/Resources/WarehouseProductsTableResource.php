<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class WarehouseProductsTableResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'article' => $this->product_article,
            'status' => [
                'name' => $this->status->toString(),
                'colorType' => $this->status->toColorType()
            ],
            'totalQuantity' => $this->warehouse->total_quantity,
            'arrived_at' => $this->warehouse->arrived_at.' '.Carbon::now()->timezone
        ];
    }
}