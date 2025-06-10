<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Attributes\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributeRetrieveInterface as RetrieveInterface;
use Modules\Product\Http\Management\Service\Attributes\Interfaces\ProductAttributesRetrieveFormatterInterface as FormatterInterface;
use Modules\Product\Models\Product;
use Modules\Warehouse\Contracts\VariationInterface;
use Modules\Warehouse\Models\ProductAttributeValue;

class SingleAttributeRetrieveService implements FormatterInterface, RetrieveInterface
{
    public function __construct(
        private array $variationCols,
        private array $attributeCols,
    ) {}

    public function getAttributes(Product $product): Collection
    {
        return $product->singleAttributes()->with([
            'attribute' => fn ($query) => $query->select([...$this->attributeCols, 'type']),
        ])->get($this->variationCols);
    }

    public function loadAttributesById(Product $product, int $variationId): VariationInterface
    {
        if (! empty($this->attributeCols)) {
            return
                $product
                    ->singleAttributes()
                    ->with([
                        'attribute' => fn ($query) => $query->select([...$this->attributeCols, 'type']),
                    ])
                    ->where('id', $variationId)
                    ->first($this->variationCols);
        }

        return $product
            ->singleAttributes()
            ->where('id', $variationId)
            ->first($this->variationCols);
    }

    public function loadAttributesByIds(BaseCollection $productsWithVariations): BaseCollection
    {
        $variationIds = $productsWithVariations->pluck('variation_id')->unique();

        $variations = ProductAttributeValue::query()
            ->with(['attribute' => fn ($query) => $query->select([...$this->attributeCols, 'type'])])
            ->whereIn('id', $variationIds)
            ->select($this->variationCols)
            ->get()
            ->keyBy('id');

        return $productsWithVariations->transform(function (array $item) use ($variations) {
            $item['product']->setRelation('singleAttributes', $variations->get($item['variation_id']));

            return $item;
        });
    }

    public function getAttributesForProductCollection(Collection $products): Collection
    {
        $relations = [
            'singleAttributes' => fn ($query) => $query->select([...$this->variationCols, 'product_id']),
        ];

        if (! empty($this->attributeCols)) {
            $relations['singleAttributes.attribute'] = fn ($query) => $query->select(
                [...$this->attributeCols, 'attribute_id', 'type'],
            );
        }

        return $products->loadMissing($relations);
    }

    public function formatPreviewAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->map(function (ProductAttributeValue $attributeValue) {
                $attributes = [
                    'id' => $attributeValue->id,
                    'name' => $attributeValue->attribute->name,
                    'value' => $attributeValue->value,
                    'price' => $attributeValue->price,
                ];

                if ($attributeValue->attribute->type) {
                    $attributes['type'] = [
                        'id' => $attributeValue->attribute->type,
                        'name' => $attributeValue->attribute->type->toTypes(),
                    ];
                }

                if ($attributeValue->attribute->view_type) {
                    $attributes['attribute_view_type'] = $attributeValue->attribute->view_type->toTypes();
                }

                return $attributes;
            })
            ->groupBy('name')
            ->map(function ($items) {
                $attributes = [
                    'name' => $items->first()['name'],
                    'data' => $items->map(fn ($item) => [
                        'id' => $item['id'],
                        'value' => $item['value'],
                        'price' => $item['price'],
                    ])->toArray(),
                ];

                if (array_key_exists('attribute_view_type', $items->first())) {
                    $attributes['attribute_view_type'] = $items->first()['attribute_view_type'];
                }

                if (array_key_exists('type', $items->first())) {
                    $attributes['type'] = $items->first()['type'];
                }

                return $attributes;
            })
            ->collapse();
    }

    public function formatWarehouseInfoAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->map(fn (ProductAttributeValue $attributeValue) => [
                'id' => $attributeValue->id,
                'name' => $attributeValue->attribute->name,
                'value' => $attributeValue->value,
                'price' => $attributeValue->price,
            ])
            ->groupBy('name')
            ->map(fn ($items) => [
                'name' => $items->first()['name'],
                'data' => $items->map(fn ($item) => [
                    'id' => $item['id'],
                    'value' => $item['value'],
                    'price' => $item['price'],
                ])->toArray(),
            ])
            ->collapse();
    }
}
