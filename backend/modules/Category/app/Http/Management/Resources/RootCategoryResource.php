<?php

declare(strict_types=1);

namespace Modules\Category\Http\Management\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class RootCategoryResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attrs = [
            'name' => $this->name,
        ];

        if ($this->slug) {
            $attrs['slug'] = $this->slug;
        }

        if ($this->icon_url) {
            $attrs['icon'] = $this->icon_url;
        }

        return $attrs;
    }
}
