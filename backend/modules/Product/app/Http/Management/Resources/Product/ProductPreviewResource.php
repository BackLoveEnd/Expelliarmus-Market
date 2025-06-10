<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Resources\Product;

use Illuminate\Http\Request;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Category\Http\Management\Resources\RootCategoryResource;
use Modules\Warehouse\Http\Resources\Warehouse\CombinedAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\SingleAttributeVariationResource;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductPreviewResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'title' => $this->title,
            'slug' => $this->slug,
            'article' => $this->product_article,
            'main_description' => $this->main_description_html,
            'title_description' => $this->title_description,
            'images' => collect($this->images)->select(['order', 'image_url']),
            'specifications' => $this->productSpecs,
        ];

        if ($this->previewAttributes) {
            $attributes['previewVariations'] = $this->previewAttributes;
        }

        return $attributes;
    }

    public function toRelationships(Request $request): array
    {
        $relationships = [
            'category' => fn () => RootCategoryResource::make($this->category),
            'brand' => fn () => BrandResource::make($this->brand),
        ];

        if ($this->resource->hasCombinedAttributes() === null) {
            return $relationships;
        }

        if ($this->resource->hasCombinedAttributes()) {
            $relationships['variations'] =
                fn () => CombinedAttributeVariationResource::collection($this->productAttributes);
        } else {
            $relationships['variations'] =
                fn () => SingleAttributeVariationResource::collection($this->productAttributes);
        }

        return $relationships;
    }
}
