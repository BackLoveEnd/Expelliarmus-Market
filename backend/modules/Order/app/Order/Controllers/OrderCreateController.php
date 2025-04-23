<?php

declare(strict_types=1);

namespace Modules\Order\Order\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Order\Order\Requests\GuestOrderCreateRequest;
use Modules\Order\Order\Services\CreateOrderService\OrderService;
use Modules\User\Http\Actions\Guests\CreateGuestAction;
use Modules\User\Http\Dto\CreateGuestDto;

class OrderCreateController extends Controller
{
    public function __construct(
        private CreateGuestAction $createGuestAction,
        private OrderService $orderService,
    ) {}

    public function __invoke(GuestOrderCreateRequest $request): JsonResponse
    {
        $user = $request->user('web');

        if (! $user) {
            $user = $this->createGuestAction->handle(
                dto: CreateGuestDto::fromRequest($request),
            );
        }

        $orderId = $this->orderService->for($user)->process();

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => [
                'order_id' => $orderId,
            ],
        ]);
    }
}