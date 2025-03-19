<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->truncate();

        User::factory(10)->create()->each(function (User $user) {
            $user->assignRole('regular_user');
        });
    }
}