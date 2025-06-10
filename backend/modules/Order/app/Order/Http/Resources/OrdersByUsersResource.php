<?php

declare(strict_types=1);

namespace Modules\Order\Order\Http\Resources;

use Illuminate\Http\Request;
use Modules\Order\Order\Models\Order;
use ReflectionClass;
use TiMacDonald\JsonApi\JsonApiResource;

class OrdersByUsersResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $user = $this->resource->first()->userable;

        return [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->fullName(),
                'phone_number' => $user->phone_number,
                'type' => strtolower((new ReflectionClass($user))->getShortName()),
                'email' => $user->email,
            ],
            'orders' => $this->resource->map(fn (Order $order) => [
                'order_id' => $order->order_id,
                'status' => $order->status->toString(),
                'total_price' => $order->total_price,
                'created_at' => $order->created_at,
            ]),
        ];
    }

    public function toId(Request $request): string
    {
        $user = $this->resource->first()->userable;

        return $user->id.':'.strtolower((new ReflectionClass($user))->getShortName());
    }

    public function toType(Request $request): string
    {
        return 'orders';
    }
}
