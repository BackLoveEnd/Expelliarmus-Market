<?php

declare(strict_types=1);

namespace Modules\Warehouse\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\Warehouse\Enums\WarehouseProductStatusEnum;
use RuntimeException;

class WarehouseBuilder extends Builder
{
    public function whereStatus(array|WarehouseProductStatusEnum $status, string $boolean = 'or'): WarehouseBuilder
    {
        $statuses = Arr::wrap($status);

        return $this->where(function ($query) use ($statuses, $boolean) {
            if ($boolean === 'or') {
                foreach ($statuses as $index => $status) {
                    if ($index === 0) {
                        $query->where('status', $status->value);
                    } else {
                        $query->orWhere('status', $status->value);
                    }
                }
            } elseif ($boolean === 'and') {
                foreach ($statuses as $status) {
                    $query->where('status', $status->value);
                }
            } elseif ($boolean === 'in') {
                $query->whereIn('status', $statuses);
            } else {
                throw new RuntimeException('Unknown boolean type in whereStatus method');
            }
        });
    }
}
