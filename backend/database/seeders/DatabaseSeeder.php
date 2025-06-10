<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Database\Seeders\CategoryDatabaseSeeder;
use Modules\ContentManagement\Database\Seeders\ContentManagementDatabaseSeeder;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;
use Modules\Warehouse\Database\Seeders\WarehouseDatabaseSeeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            CategoryDatabaseSeeder::class,
            ProductDatabaseSeeder::class,
            WarehouseDatabaseSeeder::class,
            ContentManagementDatabaseSeeder::class,
            OrderDatabaseSeeder::class,
        ]);
    }
}
