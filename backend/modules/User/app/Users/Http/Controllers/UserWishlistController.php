<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Http\Management\Support\ProductSlug;
use Modules\User\Users\Http\Exceptions\CannotAddNotPublishedProductToWishlistException;
use Modules\User\Users\Http\Exceptions\ProductDoesNotExistsInWishlistException;
use Modules\User\Users\Http\Exceptions\ProductIsAlreadyInWishlistException;
use Modules\User\Users\Http\Resources\UserWishlistResource;
use Modules\User\Users\Http\Services\Wishlist\WishlistService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserWishlistController
{
    public function __construct(
        private WishlistService $wishlistService,
    ) {}

    /**
     * Retrieve user wishlist.
     *
     * Usage place - Shop.
     */
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
                    'next_page_number' => $wishlist->hasMorePages() ? $wishlist->currentPage() + 1 : null,
                ],
            ]);
    }

    /**
     * Add product to user wishlist.
     *
     * Usage place - Shop.
     *
     * @throws ProductIsAlreadyInWishlistException
     * @throws CannotAddNotPublishedProductToWishlistException
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

    /**
     * Clear all user wishlist.
     *
     * Usage place - Shop.
     */
    public function clearWishlist(Request $request): JsonResponse
    {
        if ($this->wishlistService->clearAll($request->user())) {
            return response()->json(['message' => 'Wishlist was cleared.']);
        }

        return response()->json(['message' => 'The changes were not applied.']);
    }
}
