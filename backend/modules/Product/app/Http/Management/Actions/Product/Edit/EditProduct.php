<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Edit;

use Illuminate\Support\Collection;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpec;

class EditProduct
{
    public function __construct(
        private Product $product,
        private DeleteVariationsWhenNeedAction $deleteVariationAction
    ) {}

    public function handle(CreateProductDto $productDto): Product
    {
        $this->product->fill([
            'title' => $productDto->title,
            'title_description' => $productDto->titleDesc,
            'main_description_markdown' => $productDto->mainDesc,
            'category_id' => $productDto->categoryId,
            'brand_id' => $productDto->brandId,
            'with_attribute_combinations' => $productDto->withCombinations(),
        ]);

        if ($this->product->isDirty()) {
            if ($this->product->getOriginal('product_article') !== $productDto->productArticle) {
                $changes['product_article'] = $productDto->productArticle;
            } else {
                $changes = $this->product->getDirty();
            }

            $this->deleteVariationAction->handle($this->product, $productDto->withCombinations());

            $this->product->update($changes);
        }

        $this->updateNewSpecs($productDto->productSpecs->map(fn ($spec) => [
            'id' => $spec->id,
            'value' => $spec->value,
        ]));

        return $this->product;
    }

    private function updateNewSpecs(Collection $specs): void
    {
        $specs = $specs->map(function ($val) {
            return [
                'attribute_id' => $val['id'],
                'product_id' => $this->product->id,
                'value' => json_encode($val['value']),
            ];
        });

        $currentSpecsId = $this->product->productSpecs->map(function ($spec) {
            return $spec->pivot->attribute_id;
        });

        $toDelete = $currentSpecsId->diff($specs->pluck('attribute_id'));

        if ($toDelete->isNotEmpty()) {
            ProductSpec::query()->whereIn('attribute_id', $toDelete->toArray())->delete();
        }

        ProductSpec::query()->upsert($specs->toArray(), ['attribute_id', 'product_id'], ['value']);
    }
}
