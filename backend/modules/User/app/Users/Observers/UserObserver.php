<?php

namespace Modules\User\Users\Observers;

use App\Services\Cache\CacheService;
use Carbon\Carbon;
use Modules\User\Users\Models\User;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    public function creating(User $user): void
    {
        if ($user->user_id === null) {
            $user->user_id = Uuid::uuid7()->toString();
        }

        if ($user->created_at === null) {
            $user->created_at = Carbon::now();
        }
    }

    public function saved(User $user): void
    {
        CacheService::forgetKey('user', $user->userUuid());
    }

    public function created(User $user): void
    {
        //
    }

    public function updated(User $user): void
    {
        //
    }

    public function deleted(User $user): void
    {
        CacheService::forgetKey('user', $user->userUuid());
    }

    public function restored(User $user): void
    {
        //
    }

    public function forceDeleted(User $user): void
    {
        //
    }
}
