<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Services\Wishlist;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Product\Http\Management\Service\Attributes\Dto\FetchAttributesColumnsDto;
use Modules\Product\Models\Product;
use Modules\User\Users\Http\Exceptions\CannotAddNotPublishedProductToWishlistException;
use Modules\User\Users\Http\Exceptions\ProductDoesNotExistsInWishlistException;
use Modules\User\Users\Http\Exceptions\ProductIsAlreadyInWishlistException;
use Modules\User\Users\Models\User;
use Modules\User\Users\Models\Wishlist;
use Modules\Warehouse\Enums\ProductStatusEnum;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;

class WishlistService
{
    public function __construct(
        protected WarehouseProductInfoService $warehouseService,
    ) {}

    public function get(User $user): ?LengthAwarePaginator
    {
        $wishlists = $user
            ->wishlist()
            ->with('product:id,with_attribute_combinations,preview_image,title,slug')
            ->paginate(config('user.retrieve.user-wishlist'));

        if ($wishlists->isEmpty()) {
            return null;
        }

        $productsWithPrices = $this->warehouseService->getWarehouseInfoAboutProducts(
            products: new Collection($wishlists->getCollection()->pluck('product')),
            dto: new FetchAttributesColumnsDto(
                singleAttrCols: [['id', 'price'], []],
                combinedAttrCols: [['id', 'price'], []],
            ),
        );

        $products = $this->mapPricesForProducts($productsWithPrices);

        $wishlists->getCollection()->transform(function (Wishlist $wishlist) use ($products) {
            $wishlist->product = $products->firstWhere('id', $wishlist->product_id);

            return $wishlist;
        });

        return $wishlists;
    }

    public function add(User $user, Product $product): void
    {
        if (! $product->status->is(ProductStatusEnum::PUBLISHED)) {
            throw new CannotAddNotPublishedProductToWishlistException;
        }

        if ($user->wishlist()->where('product_id', $product->id)->exists()) {
            throw new ProductIsAlreadyInWishlistException;
        }

        DB::transaction(static function () use ($user, $product) {
            $user->wishlist()->create(['product_id' => $product->id]);
        });
    }

    public function removeProduct(User $user, Product $product): void
    {
        if (! $user->wishlist()->where('product_id', $product->id)->exists()) {
            throw new ProductDoesNotExistsInWishlistException;
        }

        $user->wishlist()->where('product_id', $product->id)->delete();
    }

    public function clearAll(User $user): bool
    {
        return (bool) $user->wishlist()->delete();
    }

    private function mapPricesForProducts(Collection $products): Collection
    {
        return $products->each(function (Product $product) {
            if (is_null($product->hasCombinedAttributes())) {
                $product->price = $product->warehouse->default_price;
            } elseif ($product->hasCombinedAttributes()) {
                $product->price = $product->combinedAttributes->first()->price;
            } else {
                $product->price = $product->singleAttributes->first()->price;
            }
        });
    }
}
