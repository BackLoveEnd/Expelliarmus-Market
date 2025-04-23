<?php

declare(strict_types=1);

namespace Modules\User\Events;

use Modules\User\Models\Guest;
use Modules\User\Models\User;

readonly class GuestRegistered
{

    public function __construct(
        public User $createdUser,
        public Guest $guest,
    ) {}

}
