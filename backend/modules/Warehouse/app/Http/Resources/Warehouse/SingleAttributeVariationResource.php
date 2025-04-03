<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Modules\Warehouse\Http\Resources\Discount\DiscountBaseResource;
use TiMacDonald\JsonApi\JsonApiResource;

class SingleAttributeVariationResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'id' => $this->id,
            'price' => $this->price,
            'discount' => $this->lastDiscount?->isEmpty() ? null : DiscountBaseResource::make(
                $this->lastDiscount->first(),
            ),
            'value' => $this->value,
            'quantity' => $this->quantity,
            'attribute_name' => $this->attribute->name,
            'attribute_id' => $this->attribute->id,
        ];

        if ($this->attribute->type) {
            $attributes['attribute_type'] = $this->attribute->type->toTypes();
            $attributes['attribute_type_id'] = $this->attribute->type->value;
        }

        if ($this->attribute->view_type) {
            $attributes['attribute_view_type'] = $this->attribute->view_type->value;
        }

        return $attributes;
    }

    public function toType(Request $request): string
    {
        return 'variations';
    }
}