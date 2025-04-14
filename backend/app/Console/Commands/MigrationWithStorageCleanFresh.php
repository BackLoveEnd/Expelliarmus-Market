<?php

namespace App\Console\Commands;

use App\Helpers\MigrationStorageCleanHelper;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Symfony\Component\Console\Input\InputOption;

class MigrationWithStorageCleanFresh extends FreshCommand
{
    public function handle(): void
    {
        parent::handle();

        if ($this->option('storage-clean')) {
            MigrationStorageCleanHelper::cleanProductStorage();

            MigrationStorageCleanHelper::cleanSliderContentStorage();

            MigrationStorageCleanHelper::cleanArrivalsContentStorage();

            MigrationStorageCleanHelper::cleanBrandsLogo();

            $this->info('Storage cleaned successfully.');
        }
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = ['storage-clean', null, InputOption::VALUE_NONE, 'Clean storage with migrations.'];

        return $options;
    }
}
