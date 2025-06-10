<?php

declare(strict_types=1);

namespace Modules\Manager\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Manager\Http\Dto\ManagerRegisterDto;
use Modules\Manager\Interfaces\ManagerAuthRepositoryInterface;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Enums\RolesEnum;

class ManagerAuthRepository implements ManagerAuthRepositoryInterface
{
    public function save(ManagerRegisterDto $managerRegisterDto): Manager
    {
        return DB::transaction(function () use ($managerRegisterDto) {
            $manager = Manager::query()->create([
                'first_name' => $managerRegisterDto->firstName,
                'last_name' => $managerRegisterDto->lastName,
                'password' => Hash::make($managerRegisterDto->tmpPassword),
                'email' => $managerRegisterDto->email,
            ]);

            $manager->assignRole(RolesEnum::MANAGER->toString());

            return $manager;
        });
    }

    public function getByEmail(string $email, array $cols = ['*']): ?Manager
    {
        return Manager::query()
            ->where('email', $email)
            ->first($cols);
    }
}
