<?php

declare(strict_types=1);

namespace Modules\Order\Order\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Order\Order\Enum\OrderStatusEnum;

class CannotChangeOrderStatusException extends Exception
{
    public static function fromStatuses(OrderStatusEnum $from, OrderStatusEnum $to): CannotChangeOrderStatusException
    {
        return new self(
            message: "Changing the order status from {$from->toString()} to {$to->toString()} is not possible.",
        );
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->message], 403);
    }
}
