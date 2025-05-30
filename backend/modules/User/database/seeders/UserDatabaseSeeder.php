<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Manager\Database\Seeders\ManagerSeeder;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserPermissionSeeder::class,
            UserSeeder::class,
            GuestSeeder::class,
            ManagerSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
