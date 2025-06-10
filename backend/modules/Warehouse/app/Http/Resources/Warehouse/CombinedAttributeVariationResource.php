<?php

declare(strict_types=1);

namespace Modules\Warehouse\Http\Resources\Warehouse;

use Illuminate\Http\Request;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use Modules\Warehouse\Http\Resources\Discount\DiscountBaseResource;
use Modules\Warehouse\Models\ProductAttribute;
use TiMacDonald\JsonApi\JsonApiResource;

class CombinedAttributeVariationResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price,
            'discount' => $this->lastDiscount?->isEmpty() ? null : DiscountBaseResource::make(
                $this->lastDiscount->first(),
            ),
            'quantity' => $this->quantity,
            'availability' => $this->quantity > 0
                ? (object) ['label' => WarehouseProductStatusEnum::IN_STOCK->toString(), 'color' => 'success']
                : (object) ['label' => WarehouseProductStatusEnum::NOT_AVAILABLE->toString(), 'color' => 'danger'],
            'attributes' => $this->productAttributes->map(function (ProductAttribute $attribute) {
                $attributes = [
                    'id' => $attribute->pivot->attribute_id,
                    'value' => $attribute->pivot->value,
                    'name' => $attribute->name,
                ];

                if ($attribute->type) {
                    $attributes['type'] = [
                        'id' => $attribute->type->value,
                        'name' => $attribute->type->toTypes(),
                    ];
                }

                if ($attribute->view_type) {
                    $attributes['attribute_view_type'] = $attribute->view_type->value;
                }

                return $attributes;
            }),
        ];
    }

    public function toType(Request $request): string
    {
        return 'variations';
    }
}
