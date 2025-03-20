<?php

declare(strict_types=1);

namespace Modules\Brand\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\Warehouse\Enums\ProductStatusEnum;
use RuntimeException;

class ProductBuilder extends Builder
{
    public function wherePublished(): ProductBuilder
    {
        return $this->where('status', ProductStatusEnum::PUBLISHED->value);
    }

    public function whereNotPublished(): ProductBuilder
    {
        return $this->where('status', ProductStatusEnum::NOT_PUBLISHED->value);
    }

    public function search(mixed $searchable): ProductBuilder
    {
        return $this
            ->whereRaw('document_search @@ plainto_tsquery(?)', [$searchable])
            ->orderByRaw('ts_rank(document_search, plainto_tsquery(?))', [$searchable]);
    }

    /**
     * @param  array<ProductStatusEnum>|ProductStatusEnum  $status
     * @param  string  $boolean
     * @return ProductBuilder
     */
    public function whereStatus(array|ProductStatusEnum $status, string $boolean = 'or'): ProductBuilder
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

    public function withoutDiscounts(): ProductBuilder
    {
        return $this
            ->whereDoesntHave('discount')
            ->whereDoesntHave('singleAttributes.discount')
            ->whereDoesntHave('combinedAttributes.discount');
    }
}