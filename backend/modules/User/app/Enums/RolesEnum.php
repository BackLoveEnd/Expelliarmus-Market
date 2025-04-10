<?php

namespace Modules\User\Enums;

enum RolesEnum: string
{
    case REGULAR_USER = 'regular_user';

    case GUEST = 'guest';

    case MANAGER = 'manager';

    case SUPER_MANAGER = 'super_manager';
}
