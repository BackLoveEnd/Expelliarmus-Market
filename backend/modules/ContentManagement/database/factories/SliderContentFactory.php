<?php

declare(strict_types=1);

namespace Modules\ContentManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ContentManagement\Models\ContentSlider;
use Ramsey\Uuid\Uuid;

class SliderContentFactory extends Factory
{
    protected $model = ContentSlider::class;

    public function definition(): array
    {
        $defaultSlider = config('contentmanagement.default.slider.image');

        return [
            'slide_id' => Uuid::uuid7()->toString(),
            'image_url' => url('/storage/content/slider/'.$defaultSlider),
            'image_source' => $defaultSlider,
            'order' => fake()->numberBetween(1, 5000),
            'content_url' => config('app.frontend_url'),
        ];
    }
}
