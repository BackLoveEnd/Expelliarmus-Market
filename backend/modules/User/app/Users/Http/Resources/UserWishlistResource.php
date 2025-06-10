<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserWishlistResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->product->id,
            'title' => $this->product->title,
            'slug' => $this->product->slug,
            'price' => $this->product->price,
            'preview_image' => $this->product->preview_image,
        ];
    }
}
