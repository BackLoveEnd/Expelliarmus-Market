<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Discount;

use Illuminate\Http\Request;
use Modules\Brand\Http\Resources\BrandResource;
use Modules\Category\Http\Management\Resources\RootCategoryResource;
use Modules\Warehouse\Http\Resources\Warehouse\CombinedAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\SingleAttributeVariationResource;
use Modules\Warehouse\Http\Resources\Warehouse\WarehouseResource;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductWarehouseDiscountsResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $variationsType = null;

        $attributes = [
            'title' => $this->title,
            'article' => $this->product_article,
            'previewImage' => $this->preview_image,
            'status' => [
                'name' => $this->status->toString(),
                'colorType' => $this->status->toColorType(),
            ],
            'variationType' => $variationsType,
        ];

        if (! is_null($this->hasCombinedAttributes())) {
            if ($this->hasCombinedAttributes()) {
                $attributes['variationType'] = 'combined';
            } else {
                $attributes['variationType'] = 'single';
            }
        }

        if ($this->lastDiscount?->isNotEmpty()) {
            $attributes['discount'] = DiscountBaseResource::make($this->lastDiscount->first());
        } else {
            $attributes['discount'] = null;
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
