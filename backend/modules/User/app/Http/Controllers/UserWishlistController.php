<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\User\Http\Exceptions\ProductDoesNotExistsInWishlistException;
use Modules\User\Http\Exceptions\ProductIsAlreadyInWishlistException;
use Modules\User\Http\Resources\UserWishlistResource;
use Modules\User\Http\Services\Wishlist\WishlistService;
use Modules\Warehouse\Http\Exceptions\CannotAddDiscountToProductWithoutPriceException;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserWishlistController
{
    public function __construct(
        private WishlistService $wishlistService,
    ) {}

    public function getUserWishlist(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $wishlist = $this->wishlistService->get($request->user());

        if (! $wishlist) {
            return response()->json(['message' => 'Wishlist is empty'], 404);
        }

        return UserWishlistResource::collection($wishlist->items())
            ->additional([
                'meta' => [
                    'total' => $wishlist->total(),
                ],
                'links' => [
                    'next' => $wishlist->nextPageUrl(),
                ],
            ]);
    }

    /**
     * Add product to user wishlist.
     *
     * Usage place - Shop.
     *
     * @param  Request  $request
     * @param  ProductSlug  $productBind
     * @return JsonResponse
     * @throws ProductIsAlreadyInWishlistException
     * @throws CannotAddDiscountToProductWithoutPriceException
     */
    public function addProductToWishList(Request $request, ProductSlug $productBind): JsonResponse
    {
        $this->wishlistService->add(
            user: $request->user(),
            product: $productBind->bind(['id', 'status']),
        );

        return response()->json(['message' => 'Product was added to wishlist.']);
    }

    /**
     * Remove product from user wishlist.
     *
     * Usage place - Shop.
     *
     * @param  Request  $request
     * @param  ProductSlug  $productSlug
     * @return JsonResponse
     * @throws ProductDoesNotExistsInWishlistException
     */
    public function removeFromWishlist(Request $request, ProductSlug $productSlug): JsonResponse
    {
        $this->wishlistService->removeProduct(
            user: $request->user(),
            product: $productSlug->bind(['id']),
        );

        return response()->json(['message' => 'Product was removed from wishlist.']);
    }

    public function clearWishlist(Request $request): JsonResponse
    {
        if ($this->wishlistService->clearAll($request->user())) {
            return response()->json(['message' => 'Wishlist was cleared.']);
        }

        return response()->json(['message' => 'The changes were not applied.']);
    }
}