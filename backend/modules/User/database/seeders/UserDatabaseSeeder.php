<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Guest;
use Modules\User\Models\User;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(4)->create();

        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);

        Guest::factory(5)->create();

        Guest::factory()->create([
            'email' => 'guest@gmail.com'
        ]);
    }
}
