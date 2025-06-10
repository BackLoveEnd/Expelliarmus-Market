<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Contracts\UserInterface;
use Modules\User\Users\Enums\RolesEnum;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductPolicy
{
    use HandlesAuthorization;

    public function before(UserInterface|Manager|null $manager, string $ability): ?true
    {
        if ($manager instanceof Manager && $manager->isSuperManager()) {
            return true;
        }

        return null;
    }

    public function manage(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('manage_product', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function publish(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('publish_product', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function lightDelete(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('trash_product', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }

    public function forceDelete(?Manager $manager): Response
    {
        return $manager?->isSuperManager()
            ? $this->allow()
            : throw new AccessDeniedHttpException('Product force deleting allowed only for main manager');
    }

    public function view(?Manager $manager): Response
    {
        return $manager?->hasPermissionTo('show_products', RolesEnum::MANAGER->toString())
            ? $this->allow()
            : throw new AccessDeniedHttpException('Access denied.');
    }
}
