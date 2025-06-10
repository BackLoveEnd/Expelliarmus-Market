<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class BrandsPaginatedResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand_name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo' => $this->logo_url,
        ];
    }
}
