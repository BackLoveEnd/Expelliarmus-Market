<?php

namespace Modules\Manager\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Manager\Models\Manager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ManagerPolicy
{
    use HandlesAuthorization;

    public function manage(?Manager $manager): Response
    {
        return $manager?->isSuperManager()
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied. Only for main manager');
    }
}
