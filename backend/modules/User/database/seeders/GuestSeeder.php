<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Users\Models\Guest;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        Guest::query()->truncate();

        Guest::factory(10)->create();
    }
}
