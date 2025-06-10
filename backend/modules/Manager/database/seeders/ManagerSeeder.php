<?php

declare(strict_types=1);

namespace Modules\Manager\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Manager\Models\Manager;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        Manager::query()->truncate();

        Manager::factory(4)->create();
    }
}
