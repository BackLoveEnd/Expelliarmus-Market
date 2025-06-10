<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\ProductSpecifications;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\DTO\Product\ProductViewSpecsDto;

class GetSpecificationsByCategoryAction
{
    public function handle(Category $category): ProductViewSpecsDto
    {
        $specifications = $category->productSpecifications;

        return new ProductViewSpecsDto(
            separatedSpecs: $this->getSeparatedSpecifications($specifications),
            groupedSpecs: $this->getSpecificationsInGroup($specifications),
            groups: $this->getGroups($specifications)
        );
    }

    public function getSeparatedSpecifications(Collection $specs): Collection
    {
        return $specs->whereNull('group_name')->select(['id', 'spec_name'])
            ->values();
    }

    public function getSpecificationsInGroup(Collection $specs): Collection
    {
        return $specs->whereNotNull('group_name')->groupBy('group_name')
            ->sortKeys()
            ->map(function (Collection $item, string $groupName) {
                return [
                    'group' => $groupName,
                    'specifications' => $item->select(['id', 'spec_name'])->toArray(),
                ];
            })
            ->values();
    }

    public function getGroups(Collection $specs): Collection
    {
        return $specs->whereNotNull('group_name')->pluck('group_name')->unique();
    }
}
