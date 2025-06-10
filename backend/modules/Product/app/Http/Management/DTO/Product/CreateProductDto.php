<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\DTO\Product;

use App\Services\Validators\JsonApiRelationsFormRequest;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class CreateProductDto extends Data
{
    private ?bool $withCombinations = null;

    public function __construct(
        public readonly string $title,
        public readonly string $titleDesc,
        public readonly string $mainDesc,
        public readonly int $categoryId,
        public readonly int $brandId,
        public readonly string $productArticle,
        /** @var Collection <int, ProductSpecsDto> $productSpecs */
        public readonly Collection $productSpecs,
    ) {}

    public function setAndGetVariationType(?bool $type): void
    {
        $this->withCombinations = $type;
    }

    public function withCombinations(): ?bool
    {
        return $this->withCombinations;
    }

    public static function fromRequest(JsonApiRelationsFormRequest $request): CreateProductDto
    {
        $categoryId = (int) $request->relation('category')['id'];

        return new self(
            title: $request->title,
            titleDesc: $request->title_description,
            mainDesc: $request->main_description,
            categoryId: $categoryId,
            brandId: (int) $request->relation('brand')['id'],
            productArticle: $request->product_article,
            productSpecs: ProductSpecsDto::collectWithCategory(
                items: $request->relation('product_specs'),
                categoryId: $categoryId
            ),
        );
    }
}
