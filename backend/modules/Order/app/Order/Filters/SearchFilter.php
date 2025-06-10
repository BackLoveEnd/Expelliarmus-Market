<?php

declare(strict_types=1);

namespace Modules\Order\Order\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;
use Spatie\QueryBuilder\Filters\Filter;

class SearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->where(function (Builder $builder) use ($value) {
            if (is_numeric($value)) {
                $builder->where('order_id', $value);
            }

            $builder->orWhereHasMorph(
                'userable',
                [User::class, Guest::class],
                function (Builder $builder) use ($value) {
                    $builder
                        ->where('email', 'like', "%{$value}%")
                        ->orWhere('phone_number', 'like', "%{$value}%");
                },
            );
        });
    }
}
