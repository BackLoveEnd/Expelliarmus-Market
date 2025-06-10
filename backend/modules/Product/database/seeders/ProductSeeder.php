<?php

declare(strict_types=1);

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $chunks = 1000;

        collect(range(1, 20))->chunk($chunks)->each(function ($chunk) {
            $chunk->each(fn () => Product::factory()->withCombinedAttributes());
            $chunk->each(fn () => Product::factory()->withSingleAttributes());
            $chunk->each(fn () => Product::factory()->withoutAttributes());
        });
    }
}
