<?php

namespace Modules\User\Users\Observers;

use Carbon\Carbon;
use Modules\User\Users\Models\Guest;
use Ramsey\Uuid\Uuid;

class GuestObserver
{
    public function creating(Guest $guest): void
    {
        if ($guest->guest_id === null) {
            $guest->guest_id = Uuid::uuid7()->toString();
        }

        if ($guest->created_at === null) {
            $guest->created_at = Carbon::now();
        }
    }

    public function created(Guest $guest): void
    {
        //
    }

    public function updated(Guest $guest): void
    {
        //
    }

    public function deleted(Guest $guest): void
    {
        //
    }

    public function restored(Guest $guest): void
    {
        //
    }

    public function forceDeleted(Guest $guest): void
    {
        //
    }
}
