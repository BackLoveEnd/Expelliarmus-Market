<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Discount;

use Illuminate\Http\Request;
use Modules\Product\Models\Product;
use TiMacDonald\JsonApi\JsonApiResource;

class DiscountedProductResource extends JsonApiResource
{
    public function toAttributes(Request $request)
    {
        if ($this->discountable instanceof Product) {
            $product = [
                'id' => $this->discountable->id,
                'title' => $this->discountable->title,
                'product_article' => $this->discountable->product_article,
                'image' => $this->discountable->preview_image,
                'slug' => $this->discountable->slug,
            ];
        } else {
            $product = [
                'id' => $this->discountable->product?->id,
                'title' => $this->discountable->product?->title,
                'product_article' => $this->discountable->product?->product_article,
                'image' => $this->discountable->product?->preview_image,
                'slug' => $this->discountable->product?->slug,
            ];
        }

        return [
            ...$product,
            'discount' => [
                'percentage' => $this->percentage,
                'discount_price' => $this->discount_price,
                'original_price' => $this->original_price,
                'status' => $this->status->toString(),
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ],
        ];
    }
}
