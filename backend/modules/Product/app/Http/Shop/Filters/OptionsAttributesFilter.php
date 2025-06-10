<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class OptionsAttributesFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (! is_array($value) || empty($value)) {
            return;
        }

        $query
            ->distinct()
            ->leftJoin('product_attribute_values as pav', 'products.id', '=', 'pav.product_id')
            ->leftJoin('product_variations as pv', 'products.id', '=', 'pv.product_id')
            ->leftJoin('variation_attribute_values as vav', 'pv.id', '=', 'vav.variation_id')
            ->where(function ($query) use ($value) {
                foreach ($value as $attributeId => $values) {
                    $query
                        ->orWhere(function ($query) use ($attributeId, $values) {
                            $query
                                ->where('pav.attribute_id', $attributeId)
                                ->whereIn('pav.value', (array) $values);
                        })
                        ->orWhere(function ($query) use ($attributeId, $values) {
                            $query
                                ->where('vav.attribute_id', $attributeId)
                                ->whereIn('vav.value', (array) $values);
                        });
                }
            });
    }
}
