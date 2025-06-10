<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ContentManagement\Models\ContentSlider;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        ContentSlider::factory(3)->create();
    }
}
