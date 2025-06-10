<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Service\Product;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductSpecAttributes;

class ProductSpecificationsService
{
    public function prepareProductSpecifications(Collection $specs): array
    {
        return [
            'grouped' => $this->getSpecificationsInGroupWithValue($specs),
            'separated' => $this->getSeparatedSpecifications($specs),
        ];
    }

    private function getSeparatedSpecifications(Collection $specs): array
    {
        return [
            'group' => null,
            'specifications' => $specs->whereNull('group_name')
                ->map(fn (ProductSpecAttributes $item) => [
                    'id' => $item->id,
                    'specification' => $item->spec_name,
                    'value' => $item->pivot->value ?? null,
                ])
                ->values()
                ->toArray(),
        ];
    }

    private function getSpecificationsInGroupWithValue(Collection $specs): array
    {
        return $specs->whereNotNull('group_name')->groupBy('group_name')
            ->sortKeys()
            ->map(function (Collection $items, string $groupName) {
                return [
                    'group' => $groupName,
                    'specifications' => $items->map(fn ($item) => [
                        'id' => $item->id,
                        'specification' => $item->spec_name,
                        'value' => $item->pivot->value ?? null,
                    ])->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }
}
