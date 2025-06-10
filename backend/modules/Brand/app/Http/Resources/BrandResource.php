<?php

namespace Modules\Brand\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class BrandResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'brand_name' => $this->name,
        ];

        if ($this->logo_url) {
            $attributes['logo_url'] = $this->logo_url;
        }

        if ($this->description) {
            $attributes['description'] = $this->description;
        }

        if ($this->slug) {
            $attributes['slug'] = $this->slug;
        }

        return $attributes;
    }
}
