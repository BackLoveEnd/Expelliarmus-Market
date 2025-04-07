<?php

declare(strict_types=1);

namespace Modules\Category\Http\Shop\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Category\Models\Category;
use Modules\Warehouse\Models\ProductAttribute;

class GetAttributesValuesForCategoryAction
{
    public function handle(int $categoryId): BaseCollection
    {
        $category = Category::query()
            ->with([
                'productAttributes:id,name,category_id,type',
                'productAttributes.singleAttributeValues:id,attribute_id,value,id',
                'productAttributes.combinedPivotAttributesValues:id,attribute_id,value,id',
            ])
            ->findOrFail($categoryId);

        return $this->mapAttributes($category->productAttributes);
    }

    protected function mapAttributes(Collection $attributes): BaseCollection
    {
        return $attributes
            ->map(function (ProductAttribute $attribute) {
                $singleValues = $attribute->singleAttributeValues->map(
                    function ($single) use ($attribute) {
                        return $attribute->type->castToType($single->getRawOriginal('value'));
                    },
                );

                $variationValues = $attribute->combinedPivotAttributesValues->map(
                    function ($variation) use ($attribute) {
                        return $attribute->type->castToType($variation->getRawOriginal('value'));
                    },
                );

                $attributes = $singleValues
                    ->collect()
                    ->merge($variationValues)
                    ->unique()
                    ->values();

                if ($attributes->isEmpty()) {
                    return null;
                }

                return (object)[
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'values' => $attributes,
                ];
            })
            ->filter()
            ->values();
    }
}