<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Resources;

use Illuminate\Http\Request;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Category\Http\Management\Resources\RootCategoryResource;
use Modules\Warehouse\Http\Resources\Discount\DiscountBaseResource;
use Modules\Warehouse\Http\Resources\Warehouse\CombinedAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\SingleAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\WarehouseResource;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductPublicInfoResource extends JsonApiResource
{
    public function toAttributes(Request $request)
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

        if (is_null($this->resource->hasCombinedAttributes())) {
            $attributes['discount'] = $this->lastDiscount?->isNotEmpty()
                ? DiscountBaseResource::make($this->lastDiscount->first())
                : null;
        }

        return $attributes;
    }

    public function toRelationships(Request $request): array
    {
        $relationships = [
            'category' => fn () => RootCategoryResource::make($this->category),
            'brand' => fn () => BrandResource::make($this->brand),
            'warehouse' => fn () => WarehouseResource::make($this->warehouse),
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
