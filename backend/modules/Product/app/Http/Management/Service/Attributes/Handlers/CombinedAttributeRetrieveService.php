<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface as RetrieveInterface;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributesRetrieveFormatterInterface as FormatterInterface;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\ProductAttribute;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;

class CombinedAttributeRetrieveService implements RetrieveInterface, FormatterInterface
{
    public function __construct(
        private array $variationCols,
        private array $attributeCols,
    ) {}

    public function getAttributes(Product $product): Collection
    {
        return $product->combinedAttributes()->with([
            'productAttributes' => fn($query) => $query->select([...$this->attributeCols, 'type']),
        ])->get($this->variationCols);
    }

    public function getAttributesForProductCollection(Collection $products): Collection
    {
        $relations = [
            'combinedAttributes' => fn($query) => $query->select([...$this->variationCols, 'product_id']),
        ];

        if (! empty($this->attributeCols)) {
            $relations['combinedAttributes.productAttributes'] = fn($query)
                => $query->select(
                [...$this->attributeCols, 'product_attributes.id'],
            );
        }

        return $products->loadMissing($relations);
    }

    public function formatPreviewAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->flatMap(function (ProductVariation $variation) {
                return $variation->productAttributes->map(function (ProductAttribute $attribute) {
                    $attributes = [
                        'name' => $attribute->name,
                        'value' => $attribute->pivot->value,
                    ];

                    if ($attribute->type) {
                        $attributes['type'] = (object)[
                            'id' => $attribute->type->value,
                            'name' => $attribute->type->toTypes(),
                        ];
                    }

                    if ($attribute->view_type) {
                        $attributes['attribute_view_type'] = $attribute->view_type->toTypes();
                    }

                    return $attributes;
                });
            })
            ->groupBy('name')
            ->map(function ($items) {
                $attributes = [
                    'name' => $items[0]['name'],
                    'value' => $items->pluck('value')->unique()->values(),
                ];

                if (array_key_exists('type', $items[0])) {
                    $attributes['type'] = $items[0]['type'];
                }

                if (array_key_exists('attribute_view_type', $items[0])) {
                    $attributes['attribute_view_type'] = $items[0]['attribute_view_type'];
                }

                return $attributes;
            })
            ->values();
    }

    public function formatWarehouseInfoAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->map(fn(ProductAttributeValue $attributeValue)
                => [
                'name' => $attributeValue->attribute->name,
                'value' => $attributeValue->value,
                'price' => $attributeValue->price,
            ])
            ->groupBy('name')
            ->map(fn($items)
                => [
                'name' => $items->first()['name'],
                'data' => $items->map(fn($item)
                    => [
                    'value' => $item['value'],
                    'price' => $item['price'],
                ])->toArray(),
            ])
            ->collapse();
    }
}