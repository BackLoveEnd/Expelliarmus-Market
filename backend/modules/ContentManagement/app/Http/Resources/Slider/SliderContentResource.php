<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Http\Resources\Slider;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class SliderContentResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'image' => $this->image_url,
            'order' => $this->order,
            'content_url' => $this->content_url,
        ];
    }
}
