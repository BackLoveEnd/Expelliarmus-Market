<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class CategoriesBrowseListResource extends JsonApiResource
{

    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
        ];
    }

}
