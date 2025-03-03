<?php

declare(strict_types=1);

namespace Modules\User\Http\Actions\Guests;

use Modules\User\Models\Guest;
use Spatie\QueryBuilder\QueryBuilder;

class GetGuestsAction
{
    public function handle(int $limit, int $offset): array
    {
        $guests = QueryBuilder::for(Guest::class)
            ->limit($limit)
            ->offset($offset)
            ->allowedSorts(['created_at', 'first_name', 'last_name'])
            ->get([
                'guest_id',
                'email',
                'first_name',
                'last_name',
                'phone_number',
                'created_at',
            ]);

        return [
            'items' => $guests,
            'additional' => [
                'meta' => [
                    'total' => Guest::query()->count(),
                    'limit' => $limit,
                    'offset' => $offset,
                ],
            ],
        ];
    }
}