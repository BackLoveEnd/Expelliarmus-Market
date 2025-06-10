<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Controllers;

use App\Helpers\RequestModel;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Order\Http\Resources\OrderLineResource;
use Modules\Order\Order\Http\Resources\OrdersByUsersResource;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Services\OrdersInfoService;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class ManagementOrdersRetrieveController extends Controller
{
    public function __construct(
        private OrdersInfoService $ordersInfoService,
    ) {}

    /**
     * Retrieve pending orders grouped by user.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getPendingOrders(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $this->authorize('view', Order::class);

        $orders = $this->ordersInfoService->pendings(
            limit: (int) $request->query('limit', config('order.retrieve.pending_orders')),
            offset: (int) $request->query('offset', 0),
        );

        if ($orders->items->isEmpty()) {
            return response()->json(['message' => 'Pending Orders not found.'], 404);
        }

        return OrdersByUsersResource::collection($orders->items->values())
            ->additional($orders->wrapMeta());
    }

    /**
     * Retrieve cancelled orders grouped by user.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getCancelledOrders(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $this->authorize('view', Order::class);

        $orders = $this->ordersInfoService->cancelled(
            limit: (int) $request->query('limit', config('order.retrieve.cancelled_orders')),
            offset: (int) $request->query('offset', 0),
        );

        if ($orders->items->isEmpty()) {
            return response()->json(['message' => 'Cancelled Orders not found.'], 404);
        }

        return OrdersByUsersResource::collection($orders->items->values())
            ->additional($orders->wrapMeta());
    }

    /**
     * Retrieve delivered orders grouped by user.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getDeliveredOrders(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $this->authorize('view', Order::class);

        $orders = $this->ordersInfoService->delivered(
            limit: (int) $request->query('limit', config('order.retrieve.delivered_orders')),
            offset: (int) $request->query('offset', 0),
        );

        if ($orders->items->isEmpty()) {
            return response()->json(['message' => 'Delivered Orders not found.'], 404);
        }

        return OrdersByUsersResource::collection($orders->items->values())
            ->additional($orders->wrapMeta());
    }

    /**
     * Retrieve refunded orders grouped by user.
     *
     * Usage place - Admin section.
     *
     * @throws AuthorizationException
     */
    public function getRefundedOrders(Request $request): JsonApiResourceCollection|JsonResponse
    {
        $this->authorize('view', Order::class);

        $orders = $this->ordersInfoService->refunded(
            limit: (int) $request->query('limit', config('order.retrieve.refunded_orders')),
            offset: (int) $request->query('offset', 0),
        );

        if ($orders->items->isEmpty()) {
            return response()->json(['message' => 'Refunded Orders not found.'], 404);
        }

        return OrdersByUsersResource::collection($orders->items->values())
            ->additional($orders->wrapMeta());
    }

    /**
     * Retrieve order lines for order.
     *
     * Usage place - Admin section.
     */
    public function getOrderLines(Request $request, RequestModel $model): JsonApiResourceCollection|JsonResponse
    {
        $order = $model->bind(Order::class, ['id']);

        $orderLines = $this->ordersInfoService->getOrderLinesByOrder($order);

        if ($orderLines->isEmpty()) {
            return response()->json(['message' => 'Order lines for this product not found.'], 404);
        }

        return OrderLineResource::collection($orderLines);
    }
}
