<?php

declare(strict_types=1);

namespace Modules\Order\Order\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Manager\Models\Manager;
use Modules\Order\Order\Models\Order;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Enums\RolesEnum;
use Modules\User\Users\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderPolicy
{
    use HandlesAuthorization;

    public function before(UserInterface|Manager|null $user, string $ability): ?true
    {
        if ($user instanceof Manager) {
            return true;
        }

        return null;
    }

    public function view(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('orders-view', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function cancel(User $user, Order $order): Response
    {
        if ($order->userable_id === $user->id && $order->userable_type === User::class) {
            return $this->allow();
        }

        throw new AccessDeniedHttpException('Access denied.');
    }
}
