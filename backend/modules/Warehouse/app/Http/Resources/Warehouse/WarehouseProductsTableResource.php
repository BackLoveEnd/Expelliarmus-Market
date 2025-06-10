<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
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
                'colorType' => $this->status->toColorType(),
            ],
            'warehouse_status' => [
                'name' => WarehouseProductStatusEnum::tryFrom($this->warehouse_status)?->toString(),
                'colorType' => WarehouseProductStatusEnum::tryFrom($this->warehouse_status)?->toColorType(),
            ],
            'totalQuantity' => round((float) $this->total_quantity, 2),
            'arrived_at' => $this->arrived_at,
        ];
    }
}
