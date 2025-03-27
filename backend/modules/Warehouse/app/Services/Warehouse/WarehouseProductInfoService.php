<?php

declare(strict_types=1);

namespace Modules\Warehouse\Services\Warehouse;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto as AttributesColsDto;
use Modules\Product\Http\Management\Service\Attributes\Handlers\ProductAttributeHandler;
use Modules\Product\Http\Management\Service\Attributes\Handlers\ProductAttributeService;
use Modules\Product\Models\Product;
use RuntimeException;

class WarehouseProductInfoService
{
    public function __construct(
        protected ProductAttributeService $productAttributeService,
    ) {}

    public function searchProducts(mixed $searchable): Collection
    {
        return Product::withTrashed()
            ->search($searchable)
            ->get(['id', 'title', 'product_article']);
    }

    public function getWarehouseInfoAboutProduct(int $productId): Product
    {
        $product = $this->loadProduct($productId);

        if (is_null($product->hasCombinedAttributes())) {
            return $product;
        }

        $product->productAttributes = $this->getProductAttributes(
            product: $product,
            dto: new AttributesColsDto(
                singleAttrCols: [
                    ['id', 'price', 'attribute_id', 'value', 'quantity'],
                    ['id', 'name', 'type'],
                ],
                combinedAttrCols: [
                    ['id', 'sku', 'price', 'quantity'],
                    ['product_attributes.id', 'name', 'type'],
                ],
            ),
        );

        return $product;
    }

    public function getWarehouseInfoAboutProducts(Collection $products, AttributesColsDto $dto): Collection
    {
        [$withoutVariationProducts, $withVariationProducts] = $products->partition(
            fn(Product $product) => is_null($product->hasCombinedAttributes()),
        );

        $withoutVariationProducts = $withoutVariationProducts->load([
            'warehouse' => fn($query) => $query->select($dto->warehouseCols),
        ]);

        [$combinedVariationProducts, $singleVariationProducts] = $this->getLoadedVariations(
            unloadVariations: $withVariationProducts,
            dto: $dto,
        );

        return $withoutVariationProducts
            ->merge($combinedVariationProducts)
            ->merge($singleVariationProducts);
    }

    public function getProductAttributes(Product $product, AttributesColsDto $dto): BaseCollection
    {
        $this->productAttributeService->setProduct($product);

        if ($product->hasCombinedAttributes()) {
            return $this->getCombinedAttributesProduct($dto->combinedAttrCols);
        }

        return $this->getSingleAttributesProduct($dto->singleAttrCols);
    }

    public function getProductAttributeById(Product $product, int $variationId, AttributesColsDto $dto): Product
    {
        if (is_null($product->hasCombinedAttributes())) {
            throw new RuntimeException("Product $product->id does not have variation in ".__CLASS__);
        }

        $this->productAttributeService->setProduct($product);

        if ($product->hasCombinedAttributes()) {
            return $product->setRelation(
                relation: 'combinedAttributes',
                value: $this->productAttributeService
                    ->setAttributesColumns(...$dto->combinedAttrCols)
                    ->loadAttributeById($variationId),
            );
        }

        return $product->setRelation(
            relation: 'singleAttributes',
            value: $product->singleAttributes = $this->productAttributeService
                ->setAttributesColumns(...$dto->singleAttrCols)
                ->loadAttributeById($variationId),
        );
    }

    public function getAttributeServiceHandler(): ProductAttributeHandler
    {
        return $this->productAttributeService->getAttributeHandler();
    }

    private function loadProduct(int $productId): Product
    {
        return Product::withTrashed()->where('id', $productId)
            ->with([
                'category:id,name',
                'brand:id,name',
                'warehouse',
            ])
            ->firstOrFail([
                'id',
                'title',
                'preview_image',
                'category_id',
                'brand_id',
                'product_article',
                'status',
                'with_attribute_combinations',
            ]);
    }

    private function getSingleAttributesProduct(array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->getAttributes();
    }

    private function getCombinedAttributesProduct(array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->getAttributes();
    }

    private function getLoadedVariations(Collection $unloadVariations, AttributesColsDto $dto): array
    {
        [$combinedVariationProducts, $singleVariationProducts] = $unloadVariations->partition(
            fn(Product $product) => $product->hasCombinedAttributes(),
        );

        $combinedVariationProducts = $this->getLoadedCombinedVariations(
            variations: $combinedVariationProducts,
            columns: $dto->combinedAttrCols,
        );

        $singleVariationProducts = $this->getLoadedSingleVariations(
            variations: $singleVariationProducts,
            columns: $dto->singleAttrCols,
        );

        return [$combinedVariationProducts, $singleVariationProducts];
    }

    private function getLoadedCombinedVariations(Collection $variations, array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->combinedHandler()
            ->getAttributesForCollection($variations);
    }

    private function getLoadedSingleVariations(Collection $variations, array $columns): Collection
    {
        return $this->productAttributeService
            ->setAttributesColumns(...$columns)
            ->singleHandler()
            ->getAttributesForCollection($variations);
    }
}