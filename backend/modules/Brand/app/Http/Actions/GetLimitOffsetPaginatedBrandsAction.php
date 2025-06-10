<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Actions;

use App\Services\Pagination\LimitOffsetDto;
use Modules\Brand\Models\Brand;
use Spatie\QueryBuilder\QueryBuilder;

class GetLimitOffsetPaginatedBrandsAction
{
    public function handle(array $columns, int $limit, int $offset): LimitOffsetDto
    {
        $brands = QueryBuilder::for(Brand::class)
            ->offset($offset)
            ->limit($limit)
            ->orderBy('id')
            ->get($columns);

        return new LimitOffsetDto(
            items: $brands,
            total: Brand::query()->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}
