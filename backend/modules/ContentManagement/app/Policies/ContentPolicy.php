<?php

namespace Modules\ContentManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Enums\RolesEnum;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ContentPolicy
{
    use HandlesAuthorization;

    public function before(UserInterface|Manager|null $user, string $ability): ?true
    {
        if ($user instanceof Manager && $user->isSuperManager()) {
            return true;
        }

        return null;
    }

    public function view(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('show_content_management', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function manage(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('manage_contents', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }
}
