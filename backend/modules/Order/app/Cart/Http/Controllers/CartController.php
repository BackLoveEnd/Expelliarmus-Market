<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Cart\Dto\CartProductsQuantityDto;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Http\Requests\AddToCartRequest;
use Modules\Order\Cart\Http\Requests\UpdateProductsQuantityRequest;
use Modules\Order\Cart\Http\Resources\UserCartResource;
use Modules\Order\Cart\Services\Cart\ClientCartService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CartController extends Controller
{
    public function __construct(
        private ClientCartService $service,
    ) {}

    /**
     * Retrieve cart info for user.
     *
     * Usage place - Shop.
     */
    public function getClientCart(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $cart = $this->service->getCart($request->user());

        if (! $cart) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        return UserCartResource::collection(collect($cart));
    }

    /**
     * Save product to user cart.
     *
     * Usage place - Shop.
     */
    public function addProductToCart(AddToCartRequest $request): JsonResponse
    {
        $this->service->addToCart(
            user: $request->user(),
            dto: ProductCartDto::fromRequest($request),
        );

        return response()->json(['message' => 'Product was added to cart.']);
    }

    /**
     * Update quantities for products in cart.
     *
     * Usage place - Shop.
     */
    public function updateProductsQuantity(UpdateProductsQuantityRequest $request): JsonResponse
    {
        $this->service->updateProductsQuantities(
            user: $request->user(),
            dto: CartProductsQuantityDto::fromRequest($request),
        );

        return response()->json(['message' => 'Cart was updated.']);
    }

    /**
     * Remove concrete product from user cart.
     *
     * Usage place - Shop.
     */
    public function removeFromCart(Request $request, string $id): JsonResponse
    {
        $this->service->removeFromCart(
            user: $request->user(),
            id: $id,
        );

        return response()->json(['message' => 'Done.']);
    }

    /**
     * Clear all cart data.
     */
    public function clearCart(Request $request): JsonResponse
    {
        if ($this->service->isCartEmpty($request->user())) {
            return response()->json(['message' => 'Cart is empty.'], 404);
        }

        $this->service->clearCart($request->user());

        return response()->json(['message' => 'Cart was cleared.']);
    }
}
