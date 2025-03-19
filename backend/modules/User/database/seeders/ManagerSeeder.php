<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Manager;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        Manager::query()->truncate();

        Manager::factory(4)->create()->each(function (Manager $manager) {
            $manager->assignRole('manager');
        });

        Manager::factory()->superManager()->create()->assignRole('manager');
    }
}