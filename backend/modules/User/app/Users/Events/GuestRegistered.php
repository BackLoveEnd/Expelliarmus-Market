<?php

declare(strict_types=1);

namespace Modules\User\Users\Events;

use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;

readonly class GuestRegistered
{
    public function __construct(
        public User $createdUser,
        public Guest $guest,
    ) {}
}
