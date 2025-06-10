<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class ProductsShopCardResource extends JsonApiResource
{
    public function toAttributes(Request $request)
    {
        $attributes = [
            'title' => $this->title,
            'image' => $this->preview_image,
            'slug' => $this->slug,
        ];

        $discountColumns = [
            'percentage',
            'discount_price',
            'original_price',
            'start_date',
            'end_date',
        ];

        if (is_null($this->resource->hasCombinedAttributes())) {
            $attributes['price'] = $this->warehouse->default_price;

            $attributes['discount'] = $this->discount->isNotEmpty()
                ? $this->discount->first()->only($discountColumns)
                : null;
        } elseif ($this->resource->hasCombinedAttributes()) {
            $variation = $this->combinedAttributes->first();

            $attributes['price'] = $variation->price;

            $attributes['discount'] = $variation->discount->isNotEmpty()
                ? $variation->discount->first()->only($discountColumns)
                : null;
        } else {
            $variation = $this->singleAttributes->first();

            $attributes['price'] = $variation->price;

            $attributes['discount'] = $variation->discount->isNotEmpty()
                ? $variation->discount->first()->only($discountColumns)
                : null;
        }

        return $attributes;
    }
}
