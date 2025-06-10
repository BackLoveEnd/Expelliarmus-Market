<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class TrashedProductsResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'article' => $this->product_article,
            'preview_image' => $this->preview_image,
            'quantity' => $this->warehouse->total_quantity,
            'status' => [
                'name' => $this->status->toString(),
                'colorType' => $this->status->toColorType(),
            ],
            'deleted_at' => $this->deleted_at->format('Y-m-d H:i').' '.Carbon::now()->timezone,
        ];
    }
}
