<?php

declare(strict_types=1);

namespace Modules\Order\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Order\Order\Events\OrderCreated;
use Modules\User\Models\User;

class ClearUserCartListener implements ShouldQueue
{
    public $queue = 'high';

    public function __construct(
        private CartStorageService $cartStorage,
    ) {}

    public function handle(OrderCreated $event): void
    {
        if ($event->user instanceof User) {
            $this->cartStorage->clearCart($event->user);
        } else {
            $this->cartStorage->clearSessionCart();
        }
    }
}