<?php

declare(strict_types=1);

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\Guest;

class GuestFactory extends Factory
{

    protected $model = Guest::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->email(),
            'phone_country_code' => '+380',
            'phone_number' => fake()->numberBetween(100000000, 999999999)
        ];
    }
}