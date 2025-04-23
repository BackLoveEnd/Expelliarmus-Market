<?php

declare(strict_types=1);

namespace Modules\User\Events;

use Modules\User\Models\Guest;
use Modules\User\Models\User;

class GuestRegistered
{
    public function __construct(
        public readonly User $createdUser,
        public readonly Guest $guest,
    ) {}
}