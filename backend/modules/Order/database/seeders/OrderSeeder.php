<?php

declare(strict_types=1);

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Order\Models\Order;
use Modules\Order\Order\Models\OrderLine;
use Modules\Product\Models\Product;
use Modules\User\Users\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $this->generalFake();

        $this->onlyForUser();
    }

    private function generalFake(): void
    {
        $orders = Order::factory(5)->user(type: 0)->create();

        $orders->each(function (Order $order) {
            $orderLine1 = OrderLine::factory()
                ->product(productType: Product::WITHOUT_OPTIONS)
                ->for($order)
                ->create();

            $orderLine2 = OrderLine::factory()
                ->product(productType: Product::SINGLE_OPTION)
                ->for($order)
                ->create();

            $order->update([
                'total_price' => round(
                    $orderLine1->total_price + $orderLine2->total_price,
                    2,
                ),
            ]);
        });
    }

    private function onlyForUser(): void
    {
        $orders = Order::factory(5)
            ->user(user: User::factory()->create())
            ->has(
                OrderLine::factory()
                    ->count(2)
                    ->product(productType: Product::SINGLE_OPTION),
                'orderLines',
            )
            ->create();

        foreach ($orders as $order) {
            $order->update(['total_price' => $order->orderLines->sum('total_price')]);
        }
    }
}
