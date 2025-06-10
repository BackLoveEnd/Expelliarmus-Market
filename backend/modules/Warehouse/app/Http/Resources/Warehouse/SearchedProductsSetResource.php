<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class SearchedProductsSetResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'article' => $this->product_article,
        ];
    }
}
