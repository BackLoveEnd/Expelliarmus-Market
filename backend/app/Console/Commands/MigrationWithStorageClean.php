<?php

namespace App\Console\Commands;

use App\Helpers\MigrationStorageCleanHelper;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Migrations\Migrator;

class MigrationWithStorageClean extends MigrateCommand
{
    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        $this->signature .= '
                {--storage-clean : Clean storage with migration.}
        ';

        parent::__construct($migrator, $dispatcher);
    }

    public function handle(): void
    {
        if ($this->option('storage-clean')) {
            MigrationStorageCleanHelper::cleanProductStorage();

            MigrationStorageCleanHelper::cleanSliderContentStorage();

            MigrationStorageCleanHelper::cleanArrivalsContentStorage();

            MigrationStorageCleanHelper::cleanBrandsLogo();

            $this->info('Storage cleaned successfully.');
        }

        parent::handle();
    }
}
