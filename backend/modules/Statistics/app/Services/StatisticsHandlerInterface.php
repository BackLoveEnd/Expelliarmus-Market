<?php

namespace Modules\Statistics\Services;


use Illuminate\Database\Eloquent\Builder;

interface StatisticsHandlerInterface
{
    /**
     * Here you apply query builder on condition you want to get statistics.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    public function handle(Builder $builder): Builder;

    /**
     * Here you define string identifier, by which you can get statistics  from the collection.
     * @return string
     */
    public function identifier(): string;
}