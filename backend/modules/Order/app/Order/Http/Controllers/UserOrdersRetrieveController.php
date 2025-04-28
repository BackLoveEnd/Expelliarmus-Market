<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Order\Http\Resources\UserOrdersResource;
use Modules\Order\Order\Services\UserOrdersService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class UserOrdersRetrieveController extends Controller
{
    public function __construct(
        private UserOrdersService $userOrdersService,
    ) {}

    /**
     * Retrieve the order history of the authenticated user.
     *
     * Usage place - Shop.
     *
     * @param  Request  $request
     * @return JsonApiResourceCollection
     */
    public function getOrderHistory(Request $request): JsonApiResourceCollection
    {
        $orders = $this->userOrdersService->getOrders(
            user: $request->user('web'),
            limit: config('order.retrieve.user_orders'),
        );

        return UserOrdersResource::collection($orders->getCollection())
            ->additional([
                'links' => [
                    'first' => $orders->url(1),
                    'last' => $orders->url($orders->lastPage()),
                    'next' => $orders->nextPageUrl(),
                    'prev' => $orders->previousPageUrl(),
                ],
                'meta' => [
                    'total' => $orders->total(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ],
            ]);
    }
}