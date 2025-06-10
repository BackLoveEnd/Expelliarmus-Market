<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class ExploredProductsResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->preview_image,
            'price' => $this->price,
        ];
    }
}
