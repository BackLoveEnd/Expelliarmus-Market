<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
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
     */
    public function getOrderHistory(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $orders = $this->userOrdersService->getOrders(
            user: $request->user('web'),
            limit: config('order.retrieve.user_orders'),
        );

        if ($orders->getCollection()->isEmpty()) {
            return response()->json(['message' => 'No orders found.'], 404);
        }

        return $this->formatUserOrders($orders);
    }

    /**
     * Retrieve the cancelled orders of the authenticated user.
     *
     * Usage place - Shop.
     */
    public function getCancelledOrders(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $orders = $this->userOrdersService->getCancelledOrders(
            user: $request->user('web'),
            limit: config('order.retrieve.user_orders'),
        );

        if ($orders->getCollection()->isEmpty()) {
            return response()->json(['message' => 'No cancelled orders found.'], 404);
        }

        return $this->formatUserOrders($orders);
    }

    protected function formatUserOrders(LengthAwarePaginator $orders): JsonApiResourceCollection
    {
        return UserOrdersResource::collection($orders->getCollection())
            ->additional([
                'links' => [
                    'first' => 1,
                    'last' => $orders->lastPage(),
                    'next' => $orders->hasMorePages() ? $orders->currentPage() + 1 : null,
                    'prev' => $orders->currentPage() > 1 ? $orders->currentPage() - 1 : null,
                ],
                'meta' => [
                    'total' => $orders->total(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                ],
            ]);
    }
}
