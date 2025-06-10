<?php

namespace Modules\User\Users\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\User\Users\Events\UserLogin;
use Throwable;

class SyncUserCartAfterLoginListener
{
    public function __construct(
        private CartStorageService $cartStorageService,
    ) {
        //
    }

    public function handle(UserLogin $event): void
    {
        try {
            $this->cartStorageService->syncSessionCartAfterLogin($event->user);

            $this->cartStorageService->clearSessionCart();

            $this->cartStorageService->getCart($event->user);
        } catch (Throwable $e) {
            Log::error('Failed to update cart', [$e->getMessage(), $e->getFile()]);
        }
    }
}
