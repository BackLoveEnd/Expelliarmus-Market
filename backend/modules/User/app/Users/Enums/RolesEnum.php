<?php

namespace Modules\User\Users\Enums;

enum RolesEnum: string
{
    case REGULAR_USER = 'regular_user';

    case GUEST = 'guest';

    case MANAGER = 'manager';

    case SUPER_MANAGER = 'super_manager';

    public function toString(): string
    {
        return match ($this) {
            self::REGULAR_USER => 'regular_user',
            self::GUEST => 'guest',
            self::MANAGER => 'manager',
            self::SUPER_MANAGER => 'super_manager',
        };
    }
}
