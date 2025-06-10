<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\Discount;

class DiscountedProductsSeeder extends Seeder
{
    public function run(): void
    {
        $chunks = 1000;

        collect(range(1, 3))->chunk($chunks)->each(function ($chunk) {
            $chunk->each(function () {
                Discount::factory()->product(Product::factory()->published()->withCombinedAttributes())->create();
            });

            $chunk->each(function () {
                Discount::factory()->product(Product::factory()->published()->withSingleAttributes())->create();
            });

            $chunk->each(function () {
                Discount::factory()->product(Product::factory()->published()->withoutAttributes())->create();
            });
        });
    }
}
