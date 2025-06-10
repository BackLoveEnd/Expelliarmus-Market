<?php

declare(strict_types=1);

namespace Modules\Manager\Http\Actions;

use App\Services\Pagination\LimitOffsetDto;
use Modules\Manager\Models\Manager;
use Spatie\QueryBuilder\QueryBuilder;

class GetManagersPaginatedAction
{
    public function handle(int $limit, int $offset): LimitOffsetDto
    {
        $managers = QueryBuilder::for(Manager::class)
            ->allowedSorts([
                'first_name',
                'last_name',
                'created_at',
            ])
            ->where('is_super_manager', false)
            ->limit($limit)
            ->offset($offset)
            ->get([
                'id',
                'manager_id',
                'first_name',
                'last_name',
                'email',
                'created_at',
                'is_super_manager',
            ]);

        return new LimitOffsetDto(
            items: $managers,
            total: Manager::query()->where('is_super_manager', false)->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}
