<?php

declare(strict_types=1);

namespace Modules\User\Http\Actions\RegularUsers;

use Modules\User\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class GetRegularCustomersAction
{
    public function handle(int $limit, int $offset): array
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

        return [
            'items' => $users,
            'additional' => [
                'meta' => [
                    'total' => User::query()->count(),
                    'limit' => $limit,
                    'offset' => $offset,
                ],
            ],
        ];
    }
}