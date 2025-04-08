<?php

namespace App\Console\Commands;

use App\Helpers\MigrationStorageCleanHelper;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Symfony\Component\Console\Input\InputOption;

class MigrationWithStorageCleanRollback extends RollbackCommand
{
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

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = ['storage-clean', null, InputOption::VALUE_NONE, 'Clean storage with migrations.'];

        return $options;
    }
}
