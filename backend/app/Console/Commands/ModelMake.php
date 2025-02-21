<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputArgument;

class ModelMake extends ModelMakeCommand
{
    protected function getDefaultNamespace($rootNamespace): string
    {
        $module = $this->argument('module');

        return 'Modules\\'.$module.'\\Models';
    }

    protected function configure(): void
    {
        parent::configure();

        $this->getDefinition()->addArgument(
            new InputArgument('module', InputArgument::REQUIRED, 'Module path to store model')
        );
    }
}
