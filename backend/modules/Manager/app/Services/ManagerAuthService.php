<?php

declare(strict_types=1);

namespace Modules\Manager\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Modules\Manager\Events\ManagerCreated;
use Modules\Manager\Http\Dto\ManagerRegisterDto;
use Modules\Manager\Interfaces\ManagerAuthRepositoryInterface;
use Modules\Manager\Models\Manager;

class ManagerAuthService
{
    public function __construct(
        protected ManagerAuthRepositoryInterface $authRepository,
    ) {}

    public function register(ManagerRegisterDto $managerRegisterDto): void
    {
        $managerRegisterDto->setTempPassword();

        $manager = $this->authRepository->save($managerRegisterDto);

        event(new ManagerCreated($manager, $managerRegisterDto->tmpPassword));
    }

    public function prepareToLogin(object $loginData): Manager
    {
        $manager = $this->authRepository->getByEmail($loginData->email);

        if (! $manager || ! Hash::check($loginData->password, $manager->password)) {
            throw new ModelNotFoundException;
        }

        return $manager;
    }
}
