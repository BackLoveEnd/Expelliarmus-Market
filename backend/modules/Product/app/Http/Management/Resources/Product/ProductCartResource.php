<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Resources\Product;

use Illuminate\Http\Request;
use Modules\Warehouse\Enums\ProductStatusEnum;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductCartResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'article' => $this->product_article,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'preview_image' => $this->preview_image,
            'published' => $this->status->is(ProductStatusEnum::PUBLISHED),
            'status' => [
                'name' => $this->status->toString(),
                'colorType' => $this->status->toColorType(),
            ],
        ];
    }

    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    public function toType(Request $request): string
    {
        return 'products';
    }
}
