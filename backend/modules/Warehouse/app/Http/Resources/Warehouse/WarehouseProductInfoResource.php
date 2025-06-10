<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Category\Http\Management\Resources\RootCategoryResource;
use TiMacDonald\JsonApi\JsonApiResource;

class WarehouseProductInfoResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $variationsType = null;

        if (! is_null($this->hasCombinedAttributes())) {
            if ($this->hasCombinedAttributes()) {
                $variationsType = 'combined';
            } else {
                $variationsType = 'single';
            }
        }

        return [
            'title' => $this->title,
            'article' => $this->product_article,
            'previewImage' => $this->preview_image,
            'status' => [
                'name' => $this->status->toString(),
                'colorType' => $this->status->toColorType(),
            ],
            'variationType' => $variationsType,
        ];
    }

    public function toRelationships(Request $request): array
    {
        $relationships = [
            'category' => fn () => RootCategoryResource::make($this->category),
            'brand' => fn () => BrandResource::make($this->brand),
            'warehouse' => fn () => WarehouseResource::make($this->warehouse),
        ];

        if (is_null($this->hasCombinedAttributes())) {
            return $relationships;
        }

        if ($this->hasCombinedAttributes()) {
            $relationships['variations'] =
                fn () => CombinedAttributeVariationResource::collection($this->productAttributes);
        } else {
            $relationships['variations'] =
                fn () => SingleAttributeVariationResource::collection($this->productAttributes);
        }

        return $relationships;
    }
}
