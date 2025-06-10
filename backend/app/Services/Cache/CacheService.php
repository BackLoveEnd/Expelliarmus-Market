<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function __construct(
        private Repository $repository
    ) {}

    public function key(string $configKey, string|int|null $identifier = null): string
    {
        return $identifier === null ? $configKey : sprintf($configKey, $identifier);
    }

    public static function forgetKey(string $configKey, string|int|null $identifier = null): void
    {
        if ($identifier === null) {
            Cache::forget($configKey);
        } else {
            Cache::forget(sprintf($configKey, $identifier));
        }
    }

    public function repo(): Repository
    {
        return $this->repository;
    }
}
