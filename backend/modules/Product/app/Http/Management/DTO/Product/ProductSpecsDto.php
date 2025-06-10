<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO\Product;

use Modules\Product\Http\Management\Actions\ProductSpecifications\CreateManyProductSpecsAction;
use Spatie\LaravelData\Data;

class ProductSpecsDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly array $value,
    ) {}

    public static function collectWithCategory(mixed $items, int $categoryId)
    {
        $items = collect($items)->map(fn ($item) => [...$item, 'category_id' => $categoryId]);

        return (new CreateManyProductSpecsAction)->handle($items)->map(function ($item) {
            return new self(
                id: $item['id'],
                value: $item['value'],
            );
        });
    }
}
