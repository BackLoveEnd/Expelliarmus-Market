<?php

declare(strict_types=1);

namespace Modules\Order\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Cart\Dto\ProductCartDto;
use Modules\Order\Cart\Http\Requests\AddToCartRequest;
use Modules\Order\Cart\Services\ClientCartService;

class CartController extends Controller
{
    public function __construct(
        private ClientCartService $service,
    ) {}

    public function getClientCart(Request $request)
    {
        $this->service->getCart($request->user());
    }

    public function addProductToCart(AddToCartRequest $request)
    {
        $this->service->addToCart(
            user: $request->user(),
            dto: ProductCartDto::fromRequest($request),
        );
    }

    public function removeFromCart() {}
}