<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\MigrationWithStorageClean;
use App\Console\Commands\MigrationWithStorageCleanFresh;
use App\Console\Commands\MigrationWithStorageCleanRefresh;
use App\Console\Commands\MigrationWithStorageCleanRollback;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\MigrationServiceProvider as BaseMigrationServiceProvider;

class MigrationServiceProvider extends BaseMigrationServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->registerMigrateCommand();
    }

    protected function registerMigrateCommand(): void
    {
        $this->app->singleton(Migrator::class, function ($app) {
            return $app['migrator'];
        });

        $this->app->extend('command.migrate', function ($app) {
            return new MigrationWithStorageClean($app['migrator'], $app['events']);
        });

        $this->app->extend('command.migrate.fresh', function ($app) {
            return new MigrationWithStorageCleanFresh($app['migrator']);
        });

        $this->app->extend('command.migrate.refresh', function ($app) {
            return new MigrationWithStorageCleanRefresh;
        });

        $this->app->extend('command.migrate.rollback', function ($app) {
            return new MigrationWithStorageCleanRollback($app['migrator']);
        });
    }
}
