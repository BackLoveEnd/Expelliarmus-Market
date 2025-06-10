<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Manager\Models\Manager;
use Modules\User\Users\Enums\RolesEnum;

class GetSuperManagerCommand extends Command
{
    protected $signature = 'management:create-super-manager {--force}';

    protected $description = 'Command creates or retrieve a test super manager.';

    public function handle(): void
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('This command should not be run in production without --force option.');

            return;
        }

        $manager = $this->createSuperManager();

        $this->info('Your test super manager:');

        $this->table(['Email', 'Password'], [[$manager->email, 'manager123']]);

        $this->newLine();
    }

    private function createSuperManager(): Manager
    {
        return DB::transaction(function () {
            if (Manager::query()->where('is_super_manager', true)->exists()) {
                return Manager::query()->where('is_super_manager', true)->first();
            }

            return Manager::factory()->superManager()->create()->assignRole(RolesEnum::MANAGER->toString());
        });
    }
}
