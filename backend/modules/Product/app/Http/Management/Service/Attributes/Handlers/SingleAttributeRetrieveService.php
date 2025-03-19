<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface as RetrieveInterface;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributesRetrieveFormatterInterface as FormatterInterface;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\ProductAttributeValue;

class SingleAttributeRetrieveService implements RetrieveInterface, FormatterInterface
{
    public function __construct(
        private array $variationCols,
        private array $attributeCols,
    ) {}

    public function getAttributes(Product $product): Collection
    {
        return $product->singleAttributes()->with([
            'attribute' => fn($query) => $query->select(...$this->attributeCols),
        ])->get($this->variationCols);
    }

    public function formatPreviewAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->map(fn(ProductAttributeValue $attributeValue)
                => [
                'name' => $attributeValue->attribute->name,
                'type' => [
                    'id' => $attributeValue->attribute->type,
                    'name' => $attributeValue->attribute->type->toTypes(),
                ],
                'attribute_view_type' => $attributeValue->attribute->view_type->toTypes(),
                'value' => $attributeValue->value,
                'price' => $attributeValue->price,
            ])
            ->groupBy('name')
            ->map(fn($items)
                => [
                'name' => $items->first()['name'],
                'type' => $items->first()['type'],
                'attribute_view_type' => $items->first()['attribute_view_type'],
                'data' => $items->map(fn($item)
                    => [
                    'value' => $item['value'],
                    'price' => $item['price'],
                ])->toArray(),
            ])
            ->collapse();
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