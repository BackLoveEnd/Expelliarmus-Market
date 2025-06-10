<?php

declare(strict_types=1);

namespace App\Services\Pagination;

use Illuminate\Database\Eloquent\Collection;

final readonly class LimitOffsetDto
{
    public function __construct(
        public Collection $items,
        public int $total,
        public int $limit,
        public int $offset,
    ) {}

    public function wrapMeta(): array
    {
        return [
            'meta' => [
                'total' => $this->total,
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
