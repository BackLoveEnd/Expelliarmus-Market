<?php

namespace Modules\Warehouse\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Enums\RolesEnum;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function before(UserInterface|Manager|null $manager): ?true
    {
        if ($manager instanceof Manager && $manager->isSuperManager()) {
            return true;
        }

        return null;
    }

    public function view(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('show_warehouse', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function manage(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('manage_warehouse', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }
}
