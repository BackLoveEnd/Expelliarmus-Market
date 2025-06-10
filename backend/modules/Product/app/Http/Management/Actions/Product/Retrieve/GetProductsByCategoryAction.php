<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Actions\Product\Retrieve;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Category\Models\Category;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

class GetProductsByCategoryAction
{
    public function __construct(
        private ProductImagesService $imagesService,
    ) {}

    public function handle(Category $category): object
    {
        $products = $category
            ->products()->orderBy('id')
            ->paginate(config('product.retrieve.by_category'), [
                'id',
                'slug',
                'product_article',
                'title',
                'status',
                'preview_image',
                'category_id',
                'created_at',
            ]);

        $products = $this->overrideImageUrlWhenNeeded($products);

        return (object) [
            'category_id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'products' => $products,
            'pagination' => (object) [
                'total' => $products->total(),
                'next' => $products->nextPageUrl(),
            ],
        ];
    }

    private function overrideImageUrlWhenNeeded(LengthAwarePaginator $products): LengthAwarePaginator
    {
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
}
