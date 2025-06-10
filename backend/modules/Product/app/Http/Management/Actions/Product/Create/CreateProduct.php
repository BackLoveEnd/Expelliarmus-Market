<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Create;

use Illuminate\Support\Collection;
use Modules\Product\Http\Management\DTO\Product\CreateProductDto;
use Modules\Product\Http\Management\DTO\Product\ProductSpecsDto;
use Modules\Product\Http\Management\Exceptions\FailedToCreateProductException;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSpec;
use Throwable;

class CreateProduct
{
    /**
     * @throws FailedToCreateProductException
     */
    public function handle(CreateProductDto $dto): Product
    {
        try {
            $product = $this->createProduct($dto);

            $this->linkSpecsToProduct($product, $dto->productSpecs);

            return $product;
        } catch (Throwable $e) {
            throw new FailedToCreateProductException($e->getMessage(), $e);
        }
    }

    private function createProduct(CreateProductDto $dto): Product
    {
        return Product::query()->create([
            'title' => $dto->title,
            'title_description' => $dto->titleDesc,
            'main_description_markdown' => $dto->mainDesc,
            'category_id' => $dto->categoryId,
            'brand_id' => $dto->brandId,
            'product_article' => $dto->productArticle,
            'images' => ['product_preview.png'],
            'preview_image' => 'product_preview.png',
            'with_attribute_combinations' => $dto->withCombinations(),
        ]);
    }

    private function linkSpecsToProduct(Product $product, Collection $productSpecs): void
    {
        $productSpecsData = $productSpecs->map(function (ProductSpecsDto $item) use ($product) {
            return [
                'product_id' => $product->id,
                'attribute_id' => $item->id,
                'value' => json_encode($item->value, JSON_THROW_ON_ERROR),
            ];
        })->toArray();

        ProductSpec::query()->insert($productSpecsData);
    }
}
