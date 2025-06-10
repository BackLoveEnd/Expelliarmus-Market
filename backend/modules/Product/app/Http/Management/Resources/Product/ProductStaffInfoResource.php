<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Http\Resources\Warehouse\CombinedAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\SingleAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\WarehouseStaffResource;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductStaffInfoResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'article' => $this->product_article,
            'title_description' => $this->title_description,
            'main_description_markdown' => $this->main_description_markdown,
            'images' => collect($this->images)->select(['id', 'image_url', 'order']),
            'preview_image' => $this->preview_image,
            'has_combinations' => $this->with_attribute_combinations,
            'published' => $this->status->is(ProductStatusEnum::PUBLISHED),
            'category' => $this->category->id,
            'brand' => $this->brand->id,
            'created_at' => $this->created_at.' '.Carbon::now()->timezone,
            'updated_at' => $this->updated_at ? $this->updated_at.' '.Carbon::now()->timezone : null,
            'specifications' => $this->productSpecs,
        ];
    }

    public function toRelationships(Request $request): array
    {
        $relationships = [
            'warehouse' => fn () => WarehouseStaffResource::make($this->warehouse),
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
