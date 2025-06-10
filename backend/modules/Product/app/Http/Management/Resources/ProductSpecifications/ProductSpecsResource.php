<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Resources\ProductSpecifications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSpecsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'specifications',
            'attributes' => [
                'separated' => $this->separatedSpecs,
                'grouped' => $this->groupedSpecs,
                'groups' => $this->groups,
            ],
        ];
    }
}
