<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Actions;

use Modules\Brand\Models\Brand;
use Spatie\QueryBuilder\QueryBuilder;

class GetPagePaginatedBrandsAction
{
    public function handle(array $columns, int $defaultBrandsShowNumber): array
    {
        $brands = QueryBuilder::for(Brand::class)
            ->orderBy('id')
            ->paginate($defaultBrandsShowNumber, $columns);

        return [
            'items' => $brands->items(),
            'additional' => [
                'links' => [
                    'current' => $brands->url($brands->currentPage()),
                    'first' => $brands->url(1),
                    'last' => $brands->url($brands->lastPage()),
                    'next' => $brands->nextPageUrl(),
                ],
                'meta' => [
                    'total' => $brands->total(),
                ],
            ],
        ];
    }
}
