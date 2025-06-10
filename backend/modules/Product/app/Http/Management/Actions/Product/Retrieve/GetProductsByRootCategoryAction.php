<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Retrieve;

use App\Services\Cache\CacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

class GetProductsByRootCategoryAction
{
    public function __construct(
        private ProductImagesService $imagesService,
        private CacheService $cacheService,
    ) {}

    public function handle(): Collection
    {
        $rootCategories = Category::query()
            ->whereNull('parent_id')
            ->get();

        return $rootCategories->map(function (Category $category) {
            $products = $this->getProducts($category);

            return (object) [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'products' => $products->getCollection(),
                'pagination' => (object) [
                    'next' => $products->nextPageUrl(),
                    'total' => $products->total(),
                ],
            ];
        });
    }

    private function getProducts(Category $category): LengthAwarePaginator
    {
        $descendantCategoryIds = $this->getChildrenOfRootCategory($category);

        $products = $this->getProductsByCategories($descendantCategoryIds);

        $products->getCollection()->transform(function (Product $product) {
            if (! $product->preview_image) {
                $product->preview_image = $this->imagesService->getResizedImage(
                    $product,
                    new Size(
                        width: config('product.image.preview.size.width'),
                        height: config('product.image.preview.size.height'),
                    ),
                );
            }

            return $product;
        });

        return $products;
    }

    private function getChildrenOfRootCategory(Category $category): Collection
    {
        return $this->cacheService->repo()->remember(
            key: $this->cacheService->key(config('product.cache.root-category-children'), $category->id),
            ttl: now()->addWeek(),
            callback: function () use ($category) {
                return $category->descendantsAndSelf($category)->pluck('id');
            },
        );
    }

    private function getProductsByCategories(Collection $categories): LengthAwarePaginator
    {
        return Product::withoutTrashed()
            ->whereIn('category_id', $categories->toArray())
            ->orderBy('id')
            ->paginate(config('product.retrieve.by_category'), [
                'id',
                'slug',
                'product_article',
                'title',
                'preview_image',
                'category_id',
                'status',
                'created_at',
            ]);
    }
}
