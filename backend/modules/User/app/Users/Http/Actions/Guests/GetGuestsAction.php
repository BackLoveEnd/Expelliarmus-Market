<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Actions\Guests;

use App\Services\Pagination\LimitOffsetDto;
use Modules\User\Users\Models\Guest;
use Spatie\QueryBuilder\QueryBuilder;

class GetGuestsAction
{
    public function handle(int $limit, int $offset): LimitOffsetDto
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

        return new LimitOffsetDto(
            items: $guests,
            total: Guest::query()->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}
