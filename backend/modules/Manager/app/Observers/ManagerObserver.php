<?php

namespace Modules\Manager\Observers;

use Carbon\Carbon;
use Modules\Manager\Models\Manager;
use Ramsey\Uuid\Uuid;

class ManagerObserver
{
    public function creating(Manager $manager): void
    {
        if ($manager->manager_id === null) {
            $manager->manager_id = Uuid::uuid7()->toString();
        }

        if ($manager->created_at === null) {
            $manager->created_at = Carbon::now();
        }
    }

    /**
     * Handle the Manager "created" event.
     */
    public function created(Manager $manager): void
    {
        //
    }

    /**
     * Handle the Manager "updated" event.
     */
    public function updated(Manager $manager): void
    {
        //
    }

    /**
     * Handle the Manager "deleted" event.
     */
    public function deleted(Manager $manager): void
    {
        //
    }

    /**
     * Handle the Manager "restored" event.
     */
    public function restored(Manager $manager): void
    {
        //
    }

    /**
     * Handle the Manager "force deleted" event.
     */
    public function forceDeleted(Manager $manager): void
    {
        //
    }
}
