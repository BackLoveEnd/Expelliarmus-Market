<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class CategoriesChildResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'icon' => $this->icon_url,
            'last' => $this->children->isEmpty(),
            'parent' => [
                'name' => $this->parent->name,
                'slug' => $this->parent->slug,
            ],
        ];
    }
}
