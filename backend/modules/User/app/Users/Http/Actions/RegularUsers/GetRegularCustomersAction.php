<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Actions\RegularUsers;

use App\Services\Pagination\LimitOffsetDto;
use Modules\User\Users\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class GetRegularCustomersAction
{
    public function handle(int $limit, int $offset): LimitOffsetDto
    {
        $users = QueryBuilder::for(User::class)
            ->allowedSorts(['created_at', 'first_name', 'last_name'])
            ->offset($offset)
            ->limit($limit)
            ->get([
                'user_id',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'created_at',
            ]);

        return new LimitOffsetDto(
            items: $users,
            total: User::query()->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}
