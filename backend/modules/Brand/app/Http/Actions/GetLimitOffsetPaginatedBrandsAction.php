<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Actions;

use Modules\Brand\Models\Brand;
use Spatie\QueryBuilder\QueryBuilder;

class GetLimitOffsetPaginatedBrandsAction
{
    public function handle(array $columns, int $limit, int $offset): array
    {
        $brands = QueryBuilder::for(Brand::class)
            ->offset($offset)
            ->limit($limit)
            ->get($columns);

        return [
            'items' => $brands,
            'additional' => [
                'meta' => [
                    'total' => Brand::query()->count(),
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]
        ];
    }
}