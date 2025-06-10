<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Resources\NewArrivals;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class NewArrivalsContentResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'image_url' => $this->image_url,
            'arrival_url' => $this->arrival_url,
            'position' => $this->position->value,
            'content' => $this->content,
        ];
    }
}
