<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services\ProductsRetrieve;

use Spatie\QueryBuilder\AllowedSort;

readonly class SortsConnector
{
    /** @var array<int, AllowedSort> */
    private array $sorts;

    public function defineSorts(array $sorts = []): void
    {
        $this->sorts = $sorts;
    }

    public function sorts(): array
    {
        return $this->sorts;
    }
}
