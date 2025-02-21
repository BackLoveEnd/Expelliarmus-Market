<?php

declare(strict_types=1);

namespace Modules\Category\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class RootCategoryResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attrs = [
            'name' => $this->name
        ];

        if ($this->slug) {
            $attrs['slug'] = $this->slug;
        }

        return $attrs;
    }
}