<?php

declare(strict_types=1);

namespace Modules\Statistics\Http\Stats;

use Illuminate\Database\Eloquent\Builder;
use Modules\Statistics\Services\StatisticsHandlerInterface;

class TotalUsersStats implements StatisticsHandlerInterface
{
    public function handle(Builder $builder): Builder
    {
        return $builder;
    }

    public function identifier(): string
    {
        return 'total-users';
    }
}
