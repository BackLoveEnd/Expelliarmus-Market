<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\ProductSpecifications;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Product\Models\ProductSpecAttributes;

class CreateManyProductSpecsAction
{
    public function handle(Collection $newSpecs): Collection
    {
        $reMappedToDbSpecs = $this->reMapToDbFields($newSpecs);

        $existingSpecs = $this->getExistingSpecs($reMappedToDbSpecs);

        $newSpecsFromDb = $this->getDifferentSpecsFromDb($existingSpecs, $reMappedToDbSpecs);

        if ($newSpecsFromDb->isNotEmpty()) {
            $existingSpecs = collect([...$existingSpecs, ...$this->insertNewSpecs($newSpecsFromDb)]);
        }

        return $this->mergeValuesToSpecs($existingSpecs->collect(), $reMappedToDbSpecs);
    }

    private function reMapToDbFields(Collection $newSpecs): Collection
    {
        return $newSpecs->flatMap(function (array $groupOfSpecs) {
            return collect($groupOfSpecs['specifications'])->map(function (array $spec) use ($groupOfSpecs) {
                return [
                    ...$spec,
                    'group_name' => $groupOfSpecs['group'],
                    'category_id' => $groupOfSpecs['category_id'],
                ];
            });
        });
    }

    private function mergeValuesToSpecs(Collection $existingSpecs, Collection $newSpecs): Collection
    {
        $existingSpecsSorted = $existingSpecs->sortBy(['group_name', 'spec_name']);

        return $newSpecs
            ->sortBy(['group_name', 'spec_name'])
            ->map(function ($item) use ($existingSpecsSorted) {
                $existingSpec = $existingSpecsSorted->firstWhere(function ($spec) use ($item) {
                    return $spec['spec_name'] === $item['spec_name'] && $spec['group_name'] === $item['group_name'];
                });

                return [
                    'id' => $existingSpec['id'] ?? null,
                    'value' => $item['value'],
                    'spec_name' => $item['spec_name'],
                    'group_name' => $item['group_name'],
                ];
            })->values();
    }

    private function insertNewSpecs(Collection $newSpecs): EloquentCollection
    {
        $preparedValues = $newSpecs->map(fn ($spec) => [
            'spec_name' => $spec['spec_name'],
            'group_name' => $spec['group_name'] ?: null,
            'category_id' => $spec['category_id'],
        ]);

        ProductSpecAttributes::query()->insert($preparedValues->toArray());

        return ProductSpecAttributes::query()
            ->whereIn('spec_name', $preparedValues->pluck('spec_name'))
            ->get(['id', 'spec_name', 'group_name']);
    }

    private function getDifferentSpecsFromDb(EloquentCollection $existingSpecs, Collection $newSpecs): Collection
    {
        return $newSpecs->reject(function ($item) use ($existingSpecs) {
            return $existingSpecs->contains(function ($existingItem) use ($item) {
                return $existingItem->spec_name === $item['spec_name']
                    && $existingItem->group_name === $item['group_name'];
            });
        })->values();
    }

    private function getExistingSpecs(Collection $newSpecs): EloquentCollection
    {
        $specs = $newSpecs->pluck('spec_name')->unique();

        return ProductSpecAttributes::query()->whereIn('spec_name', $specs)
            ->get(['id', 'spec_name', 'group_name']);
    }
}
