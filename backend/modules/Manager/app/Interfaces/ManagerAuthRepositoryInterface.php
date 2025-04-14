<?php

namespace Modules\Manager\Interfaces;

use Modules\Manager\Http\Dto\ManagerRegisterDto;
use Modules\Manager\Models\Manager;

interface ManagerAuthRepositoryInterface
{
    public function save(ManagerRegisterDto $managerRegisterDto): Manager;

    public function getByEmail(string $email, array $cols = ['*']): ?Manager;
}
