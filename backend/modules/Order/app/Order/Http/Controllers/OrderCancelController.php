<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Order\Exceptions\FailedToCancelOrderException;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Services\CancelOrderService;

class OrderCancelController extends Controller
{
    public function __construct(
        private CancelOrderService $cancelOrderService,
    ) {}

    /**
     * Cancel user order.
     *
     * Usage place - Shop.
     *
     * @throws AuthorizationException
     * @throws FailedToCancelOrderException
     */
    public function __invoke(Request $request, Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        $this->cancelOrderService->cancel($order);

        return response()->json(['message' => 'Order successfully cancelled.']);
    }
}
